<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\Loan;
use App\Models\InclusiveRadar\Waitlist;
use App\Enums\InclusiveRadar\LoanStatus;
use App\Enums\InclusiveRadar\ResourceStatus;
use App\Enums\InclusiveRadar\WaitlistStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    /*
    |--------------------------------------------------------------------------
    | REGISTRO DE NOVO EMPRÉSTIMO
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | ATUALIZAÇÃO
    |--------------------------------------------------------------------------
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

    /*
    |--------------------------------------------------------------------------
    | EXCLUSÃO
    |--------------------------------------------------------------------------
    */

    public function delete(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {

            if ($loan->return_date === null) {
                $this->handleStockIncrement($loan->loanable, LoanStatus::RETURNED);
            }

            $loan->delete();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | DEVOLUÇÃO
    |--------------------------------------------------------------------------
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

            return $loan->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ESTOQUE OPERACIONAL
    |--------------------------------------------------------------------------
    */

    private function handleStockDecrement($item): void
    {
        if ($item->is_digital) return;

        if ($item->quantity_available <= 0) {
            throw ValidationException::withMessages([
                'stock' => 'Não há unidades disponíveis em estoque.'
            ]);
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

        $newStatus = $status === LoanStatus::DAMAGED
            ? ResourceStatus::DAMAGED
            : ResourceStatus::AVAILABLE;

        $item->update([
            'status' => $newStatus
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CONTROLE ESTRUTURAL DE ESTOQUE (TA SERVICE USA ISSO)
    |--------------------------------------------------------------------------
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
            throw ValidationException::withMessages([
                'quantity' => "Impossível reduzir estoque: existem {$activeLoans} unidades emprestadas."
            ]);
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
        if ($item->is_digital) return;

        if ($item->status->blocksLoan()) {
            throw ValidationException::withMessages([
                'status' => "O recurso está com status '{$item->status->label()}', que bloqueia empréstimos."
            ]);
        }

        if ($item->conservation_state?->blocksLoan()) {
            throw ValidationException::withMessages([
                'conservation_state' => "O estado '{$item->conservation_state->label()}' bloqueia empréstimos."
            ]);
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

        if ($exists) {
            throw ValidationException::withMessages([
                'loanable_id' => 'Este beneficiário já possui um empréstimo ativo deste recurso.'
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RELATÓRIOS
    |--------------------------------------------------------------------------
    */

    public function getOverdueLoans(): Collection
    {
        return Loan::where('status', LoanStatus::ACTIVE)
            ->where('due_date', '<', now())
            ->with(['student.person', 'loanable'])
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | FILA DE ESPERA
    |--------------------------------------------------------------------------
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
            $waitlist->update([
                'status' => WaitlistStatus::FULFILLED->value
            ]);
        }
    }
}
