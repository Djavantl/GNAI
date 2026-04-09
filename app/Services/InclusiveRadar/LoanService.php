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

    public function store(array $data): Loan
    {
        return DB::transaction(function () use ($data) {
            /* Usamos lockForUpdate para evitar condições de corrida (race conditions)
               onde dois empréstimos simultâneos poderiam ignorar o limite de estoque. */
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

    public function update(Loan $loan, array $data): Loan
    {
        return DB::transaction(function () use ($loan, $data) {

            /* Garantimos a imutabilidade do histórico do empréstimo permitindo
               apenas a edição de campos que não afetam a auditoria do item. */
            $safeData = array_intersect_key($data, array_flip(['observation']));

            if (array_key_exists('observation', $safeData)) {
                $loan->update([
                    'observation' => $safeData['observation']
                ]);
            }

            return $loan->fresh();
        });
    }

    public function delete(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {
            /* Se um empréstimo ativo for deletado (ex: erro operacional), o estoque
               deve ser restaurado imediatamente para refletir a disponibilidade real. */
            if ($loan->return_date === null) {
                $item = $loan->loanable()->lockForUpdate()->first();

                $this->handleStockIncrement($item, LoanStatus::RETURNED);

                // Verifica se há alguém na fila esperando por este item que acabou de ser liberado
                $nextWaitlist = $this->waitlistService->notifyNext($item);

                if ($nextWaitlist) {
                    auth()->user()->notify(new ItemAvailableNotification($nextWaitlist));
                }
            }

            $loan->delete();
        });
    }

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

    private function handleStockDecrement($item): void
    {
        if ($item->is_digital) return;

        if ($item->quantity_available <= 0) {
            throw new BusinessRuleException('Não há unidades disponíveis em estoque.');
        }

        $item->decrement('quantity_available');

        if ($item->quantity_available <= 0) {
            $item->update([
                'status' => ResourceStatus::IN_USE
            ]);
        }
    }

    private function handleStockIncrement($item, LoanStatus $status): void
    {
        if (!$item || $item->is_digital) return;

        $item->increment('quantity_available');

        /* Itens devolvidos com dano recebem status específico para impedir novos
           empréstimos automáticos até que passem por manutenção/inspeção. */
        $newStatus = $status === LoanStatus::DAMAGED
            ? ResourceStatus::DAMAGED
            : ResourceStatus::AVAILABLE;

        $item->update([
            'status' => $newStatus
        ]);
    }

    public function validateStockAvailability($item, int $quantity): void
    {
        if ($item->is_digital) return;

        $activeLoans = $item->exists
            ? $item->loans()
                ->whereIn('status', LoanStatus::openStatuses())
                ->count()
            : 0;

        /* Impede que a edição de um material reduza a quantidade total abaixo
           do número de itens que estão fisicamente na rua com alunos/profissionais. */
        if ($quantity < $activeLoans) {
            throw new BusinessRuleException("Impossível reduzir estoque: existem {$activeLoans} unidades emprestadas.");
        }
    }

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

    private function validateNewLoan($item, array $data): void
    {
        $this->validateBeneficiary($data);
        $this->checkActiveLoanPendency($data);
        $this->validateResourceAvailability($item);
    }

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

        /* Regra de negócio: Um mesmo beneficiário não pode ter duas unidades do
           mesmo recurso simultaneamente para garantir a rotatividade do acervo. */
        if ($exists) {
            throw new BusinessRuleException('Este beneficiário já possui um empréstimo ativo deste recurso.');
        }
    }

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

    public function getOverdueLoans(): Collection
    {
        return Loan::where('status', LoanStatus::ACTIVE)
            ->where('due_date', '<', now())
            ->with(['student.person', 'loanable'])
            ->get();
    }

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

        /* Ao efetivar o empréstimo, damos baixa automática na intenção de reserva
           do usuário mudando o status para Atendido. */
        if ($waitlist) {
            $waitlist->update([
                'status' => WaitlistStatus::FULFILLED->value
            ]);
        }
    }
}
