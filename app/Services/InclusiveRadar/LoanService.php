<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\WaitlistStatus;
use App\Models\InclusiveRadar\{Loan, ResourceStatus, ResourceType, Waitlist};
use App\Enums\InclusiveRadar\LoanStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LoanService
{
    /*
    |--------------------------------------------------------------------------
    | CRUD
    |--------------------------------------------------------------------------
    */

    public function store(array $data): Loan
    {
        return DB::transaction(function () use ($data) {
            $item = $data['loanable_type']::lockForUpdate()->findOrFail($data['loanable_id']);
            $data['loanable_type'] = $item->getMorphClass();
            $this->validateNewLoan($item, $data);
            $this->handleStockDecrement($item);

            $data['status'] = $data['status'] ?? LoanStatus::ACTIVE->value;
            $data['user_id'] = $data['user_id'] ?? auth()->id();

            $loan = Loan::create($data);

            $this->fulfillWaitlistIfExists($item, $data['student_id'] ?? null, $data['professional_id'] ?? null);

            return $loan;
        });
    }

    public function update(Loan $loan, array $data): Loan
    {
        return DB::transaction(function () use ($loan, $data) {
            $item = $loan->loanable()->lockForUpdate()->first();

            $this->validateDueDateModification($loan, $data);

            $this->processStatusTransition($loan, $item, $data);

            $loan->update($data);

            return $loan->fresh();
        });
    }

    public function delete(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {
            // Se deletar um empréstimo que ainda não foi devolvido, repõe o estoque
            if (empty($loan->return_date)) {
                $this->handleStockIncrement($loan->loanable, 'available');
            }
            $loan->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | LÓGICAS DE TRANSIÇÃO E ESTADO
    |--------------------------------------------------------------------------
    */

    private function validateDueDateModification(Loan $loan, array $data): void
    {
        if (!isset($data['due_date'])) return;

        $wasReturned = !empty($loan->return_date);
        $newDueDate = Carbon::parse($data['due_date'])->format('Y-m-d');
        $oldDueDate = Carbon::parse($loan->due_date)->format('Y-m-d');

        if ($wasReturned && $newDueDate !== $oldDueDate) {
            throw ValidationException::withMessages([
                'due_date' => 'Não é permitido alterar a previsão de entrega de um empréstimo que já foi finalizado/devolvido.'
            ]);
        }
    }

    private function processStatusTransition(Loan $loan, $item, array &$data): void
    {
        $wasReturned = !empty($loan->return_date);
        $isSettingActive = ($data['status'] ?? null) === LoanStatus::ACTIVE->value;

        $isSettingReturned = in_array($data['status'] ?? '', [
            LoanStatus::RETURNED->value,
            LoanStatus::LATE->value,
            LoanStatus::DAMAGED->value
        ]);

        // Caso 1: Reativando um empréstimo (De Devolvido para Ativo)
        if ($wasReturned && $isSettingActive) {
            $this->validateResourceAvailability($item);
            $this->handleStockDecrement($item);
            $data['return_date'] = null;
        }

        // Caso 2: Finalizando um empréstimo (De Ativo para Devolvido)
        if (!$wasReturned && $isSettingReturned) {
            $this->validateReturnStatus($loan, $data);
            $this->handleStockIncrement($item, $data['status']);
        }
    }

    public function markAsReturned(Loan $loan, array $data = []): Loan
    {
        $returnDate = now();

        $status = !empty($data['is_damaged'])
            ? LoanStatus::DAMAGED->value
            : ($returnDate->greaterThan($loan->due_date)
                ? LoanStatus::LATE->value
                : LoanStatus::RETURNED->value
            );

        return $this->update($loan, [
            'return_date' => $returnDate,
            'status'      => $status,
            'observation' => $data['observation'] ?? $loan->observation,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | VALIDAÇÕES
    |--------------------------------------------------------------------------
    */

    private function validateNewLoan($item, array $data): void
    {
        $this->checkActiveLoanPendency($data);
        $this->validateResourceAvailability($item);
    }

    private function validateResourceAvailability($item): void
    {
        if ($item->resourceStatus?->blocks_loan) {
            throw ValidationException::withMessages([
                'status' => "O recurso está com status '{$item->resourceStatus->name}', que bloqueia novos empréstimos."
            ]);
        }
    }

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

    private function validateReturnStatus(Loan $loan, array $data): void
    {
        if (empty($data['return_date'])) {
            throw ValidationException::withMessages([
                'return_date' => 'Você deve informar a data real de devolução.'
            ]);
        }

        $returnDate = Carbon::parse($data['return_date']);
        $dueDate = Carbon::parse($loan->due_date);

        if ($data['status'] === LoanStatus::RETURNED->value && $returnDate->greaterThan($dueDate)) {
            throw ValidationException::withMessages(['status' => 'Data superior ao prazo. Use "Devolvido com atraso".']);
        }

        if ($data['status'] === LoanStatus::LATE->value && $returnDate->lessThanOrEqualTo($dueDate)) {
            throw ValidationException::withMessages(['status' => 'Data dentro do prazo. Use "Devolvido no prazo".']);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GESTÃO DE ESTOQUE
    |--------------------------------------------------------------------------
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
                if ($status) $item->update(['status_id' => $status->id]);
            }
        }
    }

    private function handleStockIncrement($item, string $status): void
    {
        if ($item && isset($item->type) && !$item->type->is_digital) {
            $item->increment('quantity_available');

            $code = ($status === LoanStatus::DAMAGED->value) ? 'damaged' : 'available';
            $statusModel = ResourceStatus::where('code', $code)->first();

            if ($statusModel) {
                $item->update(['status_id' => $statusModel->id]);
            }
        }
    }

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

    public function validateStockAvailability($item, int $quantity): void
    {
        if (!isset($item->type) || $item->type->is_digital) return;

        $activeLoans = $item->exists
            ? $item->loans()->whereIn('status', [LoanStatus::ACTIVE->value, LoanStatus::LATE->value])->count()
            : 0;

        if ($quantity < $activeLoans) {
            throw ValidationException::withMessages([
                'quantity' => "Mínimo permitido: {$activeLoans} (recursos atualmente em uso)."
            ]);
        }
    }

    public function getOverdueLoans(): Collection
    {
        return Loan::overdue()->with(['student.person', 'loanable'])->get();
    }

    /**
     * Verifica se o beneficiário (aluno ou profissional) possui fila de espera
     * para o recurso que está sendo emprestado e marca como atendida (fulfilled).
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
