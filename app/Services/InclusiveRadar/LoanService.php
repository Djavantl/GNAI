<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\LoanStatus;
use App\Enums\InclusiveRadar\ResourceStatus;
use App\Enums\InclusiveRadar\WaitlistStatus;
use App\Exceptions\BusinessRuleException;
use App\Models\InclusiveRadar\Loan;
use App\Models\InclusiveRadar\Waitlist;
use App\Notifications\InclusiveRadar\ItemAvailableNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LoanService
{
    public function __construct(
        protected WaitlistService $waitlistService
    ) {}

    /**
     * RF: registra um empréstimo com trava de estoque e baixa automática de fila.
     * Uso: operação principal de empréstimo do radar inclusivo.
     */
    public function store(array $data): Loan
    {
        return DB::transaction(function () use ($data) {
            $item = $data['loanable_type']::lockForUpdate()
                ->findOrFail($data['loanable_id']);

            $data['loanable_type'] = $item->getMorphClass();

            $this->validateNewLoan($item, $data);

            $this->handleStockDecrement($item);

            $loan = Loan::create([
                ...$data,
                'status' => LoanStatus::ACTIVE,
                'return_date' => null,
                'user_id' => $data['user_id'] ?? auth()->id(),
            ]);

            $this->fulfillWaitlistIfExists(
                $item,
                $data['student_id'] ?? null,
                $data['professional_id'] ?? null
            );

            return $loan;
        });
    }

    /**
     * RF: permite ajustes seguros em campos não estruturais do empréstimo.
     * Uso: correção de observações sem alterar o histórico transacional.
     */
    public function update(Loan $loan, array $data): Loan
    {
        return DB::transaction(function () use ($loan, $data) {
            $safeData = array_intersect_key($data, array_flip(['observation']));

            if (array_key_exists('observation', $safeData)) {
                $loan->update([
                    'observation' => $safeData['observation']
                ]);
            }

            return $loan->fresh();
        });
    }

    /**
     * RF: remove um empréstimo restaurando estoque e retomando a fila quando preciso.
     * Uso: correção operacional de registros criados indevidamente.
     */
    public function delete(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {
            if ($loan->return_date === null) {
                $item = $loan->loanable()->lockForUpdate()->first();

                $this->handleStockIncrement($item, LoanStatus::RETURNED);

                $nextWaitlist = $this->waitlistService->notifyNext($item);

                if ($nextWaitlist) {
                    auth()->user()->notify(new ItemAvailableNotification($nextWaitlist));
                }
            }

            $loan->delete();
        });
    }

    /**
     * RF: finaliza o empréstimo calculando status de devolução e nova disponibilidade.
     * Uso: fluxo de devolução normal, atrasada ou com avaria.
     */
    public function markAsReturned(Loan $loan, array $data = []): Loan
    {
        return DB::transaction(function () use ($loan, $data) {

            if ($loan->return_date !== null) {
                throw new BusinessRuleException('Este empréstimo já foi finalizado.');
            }

            $item = $loan->loanable()->lockForUpdate()->first();

            $returnDate = now();
            $isDamaged = !empty($data['is_damaged']);

            $statusEnum = $isDamaged
                ? LoanStatus::DAMAGED
                : ($returnDate->greaterThan($loan->due_date)
                    ? LoanStatus::LATE
                    : LoanStatus::RETURNED);

            $loan->update([
                'return_date' => $returnDate,
                'status' => $statusEnum,
                'observation' => $data['observation'] ?? $loan->observation,
            ]);

            $this->handleStockIncrement($item, $statusEnum);

            if (!$isDamaged) {
                $nextWaitlist = $this->waitlistService->notifyNext($item);

                if ($nextWaitlist) {
                    auth()->user()->notify(new ItemAvailableNotification($nextWaitlist));
                }
            }

            return $loan->fresh();
        });
    }

    /**
     * RF: reduz o estoque disponível quando um item físico é emprestado.
     * Uso: atualização imediata do inventário no momento da saída.
     */
    private function handleStockDecrement($item): void
    {
        if ($item->is_digital) return;

        if ($item->quantity_available <= 0) {
            throw new BusinessRuleException('Não há unidades disponíveis em estoque.');
        }

        $newAvailable = $item->quantity_available - 1;

        $item->update([
            'quantity_available' => $newAvailable,
            'status' => $newAvailable <= 0
                ? ResourceStatus::IN_USE
                : $item->status,
        ]);

        $item->refresh();
    }

    /**
     * RF: devolve unidade ao estoque e recalcula o status do item retornado.
     * Uso: exclusão de empréstimo ativo ou processo de devolução.
     */
    private function handleStockIncrement($item, LoanStatus $status): void
    {
        if (!$item || $item->is_digital) return;

        $newStatus = $status === LoanStatus::DAMAGED
            ? ResourceStatus::DAMAGED
            : ResourceStatus::AVAILABLE;

        $item->update([
            'quantity_available' => $item->quantity_available + 1,
            'status'             => $newStatus,
        ]);

        $item->refresh();
    }

    /**
     * RF: impede redução de estoque abaixo do número de itens já emprestados.
     * Uso: edição de materiais e tecnologias com circulação ativa.
     */
    public function validateStockAvailability($item, int $quantity): void
    {
        if ($item->is_digital) return;

        $activeLoans = $item->exists
            ? $item->loans()
                ->whereIn('status', LoanStatus::openStatuses())
                ->count()
            : 0;

        if ($quantity < $activeLoans) {
            throw new BusinessRuleException("Impossível reduzir estoque: existem {$activeLoans} unidades emprestadas.");
        }
    }

    /**
     * RF: recalcula quantidade disponível com base no estoque total e nos empréstimos abertos.
     * Uso: suporte a cadastros e edições de itens emprestáveis do radar.
     */
    public function calculateStockForLoan($item, array $data): array
    {
        $isDigital = $data['is_digital'] ?? $item->is_digital ?? false;

        if ($isDigital) {
            $data['quantity_available'] = null;
            return $data;
        }

        $total = (int) ($data['quantity'] ?? $item->quantity ?? 0);

        $activeLoans = $item->exists
            ? $item->loans()
                ->whereIn('status', LoanStatus::openStatuses())
                ->count()
            : 0;

        $data['quantity_available'] = $total - $activeLoans;

        return $data;
    }

    /**
     * RF: consolida as validações obrigatórias antes da criação do empréstimo.
     * Uso: guarda central do fluxo de concessão de itens.
     */
    private function validateNewLoan($item, array $data): void
    {
        $this->validateBeneficiary($data);
        $this->checkActiveLoanPendency($data);
        $this->validateResourceAvailability($item);
    }

    /**
     * RF: bloqueia empréstimos de itens indisponíveis por status ou conservação.
     * Uso: proteção do fluxo operacional de retirada.
     */
    private function validateResourceAvailability($item): void
    {
        if ($item->is_digital) return;

        if ($item->status->blocksLoan()) {
            throw new BusinessRuleException("O recurso está com status '{$item->status->label()}', que bloqueia empréstimos.");
        }

        if ($item->conservation_state?->blocksLoan()) {
            throw new BusinessRuleException("O estado '{$item->conservation_state->label()}' bloqueia empréstimos.");
        }
    }

    /**
     * RF: impede múltiplos empréstimos ativos do mesmo item para o mesmo beneficiário.
     * Uso: garantia de rotatividade e integridade do acervo.
     */
    private function checkActiveLoanPendency(array $data): void
    {
        $exists = Loan::where('loanable_id', $data['loanable_id'])
            ->where('loanable_type', $data['loanable_type'])
            ->whereNull('return_date')
            ->where(function ($q) use ($data) {
                if (!empty($data['student_id'])) {
                    $q->where('student_id', $data['student_id']);
                } else {
                    $q->where('professional_id', $data['professional_id']);
                }
            })
            ->exists();

        if ($exists) {
            throw new BusinessRuleException('Este beneficiário já possui um empréstimo ativo deste recurso.');
        }
    }

    /**
     * RF: valida que o empréstimo esteja associado a um único beneficiário válido.
     * Uso: criação e validação de consistência do registro de empréstimo.
     */
    private function validateBeneficiary(array $data, ?Loan $loan = null): void
    {
        if ($loan && $loan->status !== LoanStatus::ACTIVE) {
            return;
        }

        $student = $data['student_id'] ?? null;
        $professional = $data['professional_id'] ?? null;

        if (empty($student) && empty($professional)) {
            throw new BusinessRuleException('É necessário informar um aluno ou um profissional.');
        }

        if (!empty($student) && !empty($professional)) {
            throw new BusinessRuleException('Não é permitido informar aluno e profissional ao mesmo tempo.');
        }
    }

    /**
     * RF: lista empréstimos vencidos para alertas e monitoramento operacional.
     * Uso: rotinas administrativas e notificações de atraso.
     */
    public function getOverdueLoans(): Collection
    {
        return Loan::where('status', LoanStatus::ACTIVE)
            ->where('due_date', '<', now())
            ->with(['student.person', 'loanable'])
            ->get();
    }

    /**
     * RF: baixa automaticamente a solicitação correspondente quando o empréstimo é efetivado.
     * Uso: integração entre fila de espera e concessão do item.
     */
    private function fulfillWaitlistIfExists($item, ?int $studentId, ?int $professionalId): void
    {
        $query = Waitlist::where('waitlistable_id', $item->id)
            ->where('waitlistable_type', $item->getMorphClass())
            ->whereIn('status', [
                WaitlistStatus::WAITING->value,
                WaitlistStatus::NOTIFIED->value
            ]);

        if ($studentId) {
            $query->where('student_id', $studentId);
        } elseif ($professionalId) {
            $query->where('professional_id', $professionalId);
        }

        $waitlist = $query->first();

        if ($waitlist) {
            $waitlist->update([
                'status' => WaitlistStatus::FULFILLED->value
            ]);
        }
    }
}
