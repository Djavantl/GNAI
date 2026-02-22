<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\MaintenanceStatus;
use App\Enums\InclusiveRadar\WaitlistStatus;
use App\Models\InclusiveRadar\{Loan, ResourceStatus, ResourceType, Waitlist};
use App\Enums\InclusiveRadar\LoanStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LoanService
{
    public function __construct(
        protected MaintenanceService $maintenanceService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS DE FLUXO DE EMPRÉSTIMO E DEVOLUÇÃO
    |--------------------------------------------------------------------------
    */

    /**
     * REGISTRO DE NOVO EMPRÉSTIMO
     * * IMPORTÂNCIA: Centraliza a saída de recursos, garantindo atomicidade via banco de dados.
     * * FLUXO: Bloqueia o item para edição (lockForUpdate), valida disponibilidade,
     * decrementa estoque e limpa automaticamente solicitações pendentes na fila de espera.
     */
    public function store(array $data): Loan
    {
        return DB::transaction(function () use ($data) {
            $item = $data['loanable_type']::lockForUpdate()
                ->findOrFail($data['loanable_id']);

            $data['loanable_type'] = $item->getMorphClass();

            $this->validateNewLoan($item, $data);
            $this->handleStockDecrement($item);

            $data['status'] = LoanStatus::ACTIVE->value;
            $data['return_date'] = null;
            $data['user_id'] = $data['user_id'] ?? auth()->id();

            $loan = Loan::create($data);

            $this->fulfillWaitlistIfExists(
                $item,
                $data['student_id'] ?? null,
                $data['professional_id'] ?? null
            );

            return $loan;
        });
    }

    /**
     * ATUALIZAÇÃO DE DADOS DO EMPRÉSTIMO
     * * IMPORTÂNCIA: Permite ajustes em observações ou datas enquanto o item está na rua.
     * * REGRA: Se o empréstimo já foi devolvido, apenas o campo de observação permanece editável.
     */
    public function update(Loan $loan, array $data): Loan
    {
        return DB::transaction(function () use ($loan, $data) {
            if ($loan->return_date !== null) {
                $loan->update([
                    'observation' => $data['observation'] ?? $loan->observation,
                ]);
                return $loan->fresh();
            }
            unset($data['status'], $data['return_date']);
            $loan->update($data);
            return $loan->fresh();
        });
    }

    /**
     * EXCLUSÃO DE REGISTRO
     * * IMPORTÂNCIA: Utilizado para correções de erros de lançamento.
     * * LÓGICA: Se o item ainda não tinha sido devolvido, o sistema devolve
     * automaticamente a unidade ao estoque disponível antes de apagar o registro.
     */
    public function delete(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {
            if (empty($loan->return_date)) {
                $this->handleStockIncrement($loan->loanable, false);
            }
            $loan->delete();
        });
    }

    /**
     * FINALIZAÇÃO DE EMPRÉSTIMO (DEVOLUÇÃO)
     * * IMPORTÂNCIA: Encerra o ciclo de posse e avalia a integridade do patrimônio.
     * * GATILHO: Caso o item retorne danificado, o sistema aciona automaticamente
     * o MaintenanceService para abertura de chamado técnico.
     */
    public function markAsReturned(Loan $loan, array $data = []): Loan
    {
        return DB::transaction(function () use ($loan, $data) {
            if ($loan->return_date !== null) {
                throw ValidationException::withMessages([
                    'loan' => 'Este empréstimo já foi finalizado.'
                ]);
            }

            $item = $loan->loanable()->lockForUpdate()->first();
            $returnDate = now();
            $isDamaged = !empty($data['is_damaged']);

            $status = $isDamaged
                ? LoanStatus::DAMAGED->value
                : ($returnDate->greaterThan($loan->due_date)
                    ? LoanStatus::LATE->value
                    : LoanStatus::RETURNED->value);

            $loan->update([
                'return_date' => $returnDate,
                'status' => $status,
                'observation' => $data['observation'] ?? $loan->observation,
            ]);

            $this->handleStockIncrement($item, $isDamaged);

            if ($isDamaged) {
                $hasOpenMaintenance = $item->maintenances()
                    ->where('status', '!=', MaintenanceStatus::COMPLETED->value)
                    ->exists();

                if (!$hasOpenMaintenance) {
                    $this->maintenanceService->openMaintenanceRequest($item);
                }
            }

            return $loan->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS DE GESTÃO DE ESTOQUE (COMPARTILHADOS COM TA/MPA)
    |--------------------------------------------------------------------------
    */

    /**
     * REGISTRO DE SAÍDA (DECREMENTO DE ESTOQUE)
     * * IMPORTÂNCIA: Atualiza o saldo físico no momento do empréstimo.
     * * LÓGICA: Se o estoque disponível chegar a zero, o status global do recurso
     * é alterado para 'Em Uso' (in_use).
     */
    private function handleStockDecrement($item): void
    {
        if (isset($item->type) && !$item->type->is_digital) {
            if ($item->quantity_available <= 0) {
                throw new \Exception('Não há unidades disponíveis em estoque.');
            }
            $item->decrement('quantity_available');
            if ($item->quantity_available <= 0) {
                $status = ResourceStatus::where('code', 'in_use')->first();
                if ($status) {
                    $item->update(['status_id' => $status->id]);
                }
            }
        }
    }

    /**
     * REGISTRO DE ENTRADA (INCREMENTO DE ESTOQUE)
     * * IMPORTÂNCIA: Reestabelece o saldo físico disponível.
     * * LÓGICA: Ajusta o status do recurso para 'Disponível' ou 'Danificado'
     * dependendo da integridade do item retornado.
     */
    private function handleStockIncrement($item, bool $isDamaged): void
    {
        if ($item && isset($item->type) && !$item->type->is_digital) {
            $item->increment('quantity_available');
            $code = $isDamaged ? 'damaged' : 'available';
            $statusModel = ResourceStatus::where('code', $code)->first();
            if ($statusModel) {
                $item->update(['status_id' => $statusModel->id]);
            }
        }
    }

    /**
     * CÁLCULO DINÂMICO DE SALDO DISPONÍVEL
     * * Utilizado em: Fluxo de persistência (Store/Update) de TA e MPA.
     * * IMPORTÂNCIA: Garante que o campo 'quantity_available' no banco de dados reflita
     * a realidade imediata, subtraindo o que está em posse dos beneficiários do total.
     */
    public function calculateStockForLoan($item, array $data): array
    {
        $type = ResourceType::find($data['type_id'] ?? $item->type_id);

        if ($type?->is_digital) {
            $data['quantity_available'] = null;
            return $data;
        }

        $total = (int) ($data['quantity'] ?? $item->quantity ?? 0);
        $activeLoans = $item->exists ? $item->loans()->whereNull('return_date')->count() : 0;

        $data['quantity_available'] = $total - $activeLoans;

        return $data;
    }

    /**
     * VALIDAÇÃO DE INTEGRIDADE DE ESTOQUE
     * * Este método é utilizado tanto por AssistiveTechnologyService quanto por
     * AccessibleEducationalMaterialService durante a criação ou atualização de um recurso.
     * * IMPORTÂNCIA: Impede que um gestor reduza o estoque total (quantity) para um número
     * menor do que a quantidade de itens que já estão na rua com alunos ou profissionais.
     * Sem isso, o sistema poderia gerar um "estoque negativo" ou inconsistências matemáticas.
     */
    public function validateStockAvailability($item, int $quantity): void
    {
        if (!isset($item->type) || $item->type->is_digital) return;

        $activeLoans = $item->exists
            ? $item->loans()->whereIn('status', [LoanStatus::ACTIVE->value, LoanStatus::LATE->value])->count()
            : 0;

        if ($quantity < $activeLoans) {
            throw ValidationException::withMessages([
                'quantity' => "Impossível reduzir estoque: existem atualmente {$activeLoans} unidades em uso por beneficiários."
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS DE VALIDAÇÃO E FILA DE ESPERA
    |--------------------------------------------------------------------------
    */

    /**
     * ORQUESTRADOR DE VALIDAÇÕES DE NOVO EMPRÉSTIMO
     * * IMPORTÂNCIA: Garante que todas as regras de negócio sejam satisfeitas antes
     * de autorizar a saída do recurso.
     */
    private function validateNewLoan($item, array $data): void
    {
        $this->checkActiveLoanPendency($data);
        $this->validateResourceAvailability($item);
    }

    /**
     * VERIFICAÇÃO DE STATUS DE BLOQUEIO
     * * IMPORTÂNCIA: Impede empréstimos de itens que estão marcados com status
     * restritivos (ex: Manutenção, Extraviado, Obsoleto).
     */
    private function validateResourceAvailability($item): void
    {
        if ($item->resourceStatus?->blocks_loan) {
            throw ValidationException::withMessages([
                'status' => "O recurso está com status '{$item->resourceStatus->name}', que bloqueia novos empréstimos."
            ]);
        }
    }

    /**
     * VERIFICAÇÃO DE PENDÊNCIA ATIVA
     * * IMPORTÂNCIA: Impede que um beneficiário pegue mais de uma unidade do
     * mesmo recurso ID sem ter devolvido a anterior.
     */
    private function checkActiveLoanPendency(array $data): void
    {
        $exists = Loan::where('loanable_id', $data['loanable_id'])
            ->where('loanable_type', $data['loanable_type'])
            ->whereNull('return_date')
            ->where(fn($q) => !empty($data['student_id'])
                ? $q->where('student_id', $data['student_id'])
                : $q->where('professional_id', $data['professional_id'])
            )
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'loanable_id' => 'Este beneficiário ainda possui uma pendência ativa deste recurso.'
            ]);
        }
    }

    /**
     * LISTAGEM DE ATRASOS
     * * IMPORTÂNCIA: Alimenta o dashboard e relatórios de monitoramento de prazos.
     */
    public function getOverdueLoans(): Collection
    {
        return Loan::where('status', LoanStatus::ACTIVE)
            ->where('due_date', '<', now())
            ->with(['student.person', 'loanable'])
            ->get();
    }

    /**
     * BAIXA AUTOMÁTICA EM FILA DE ESPERA
     * * IMPORTÂNCIA: Resolve a demanda de espera no momento em que o beneficiário
     * finalmente recebe o recurso desejado.
     */
    private function fulfillWaitlistIfExists($item, ?int $studentId, ?int $professionalId): void
    {
        $query = Waitlist::where('waitlistable_id', $item->id)
            ->where('waitlistable_type', $item->getMorphClass())
            ->where('status', WaitlistStatus::WAITING->value);

        if ($studentId) {
            $query->where('student_id', $studentId);
        } elseif ($professionalId) {
            $query->where('professional_id', $professionalId);
        }

        $waitlist = $query->first();
        if ($waitlist) {
            $waitlist->update(['status' => WaitlistStatus::FULFILLED->value]);
        }
    }
}
