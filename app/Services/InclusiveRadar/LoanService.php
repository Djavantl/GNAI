<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\{Loan, ResourceStatus, ResourceType};
use App\Enums\InclusiveRadar\LoanStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LoanService
{
    public function store(array $data): Loan
    {
        return DB::transaction(function () use ($data) {
            $item = $data['loanable_type']::lockForUpdate()->findOrFail($data['loanable_id']);
            $data['loanable_type'] = $item->getMorphClass();

            $this->checkActiveLoanPendency($data);

            if ($item->resourceStatus?->blocks_loan) {
                throw new \Exception("Este item não permite empréstimos no status atual.");
            }

            $this->handleStockDecrement($item);

            $data['status'] = $data['status'] ?? LoanStatus::ACTIVE->value;

            return Loan::create($data);
        });
    }

    public function update(Loan $loan, array $data): Loan
    {
        return DB::transaction(function () use ($loan, $data) {
            $item = $loan->loanable()->lockForUpdate()->first();

            // 1. Identificar transições de status importantes
            $wasReturned = !empty($loan->return_date);
            $isSettingActive = ($data['status'] ?? null) === LoanStatus::ACTIVE->value;
            $isSettingReturned = in_array($data['status'] ?? '', [
                LoanStatus::RETURNED->value,
                LoanStatus::LATE->value,
                LoanStatus::DAMAGED->value
            ]);

            // 2. VALIDAÇÃO DE "REATIVAÇÃO": De Devolvido para Ativo
            if ($wasReturned && $isSettingActive) {
                $this->handleStockDecrement($item);
                $data['return_date'] = null;
            }

            // 3. VALIDAÇÃO DE "DEVOLUÇÃO": De Ativo para Devolvido
            if (!$wasReturned && $isSettingReturned) {
                $this->validateReturnStatus($loan, $data);
                $this->handleStockIncrement($item, $data['status']);
            }

            $loan->update($data);

            return $loan->fresh();
        });
    }

    private function validateReturnStatus(Loan $loan, array $data): void
    {
        $returnStatuses = [
            LoanStatus::RETURNED->value,
            LoanStatus::LATE->value,
            LoanStatus::DAMAGED->value
        ];

        if (!in_array($data['status'] ?? '', $returnStatuses, true)) {
            return;
        }

        if (empty($data['return_date'])) {
            throw ValidationException::withMessages([
                'return_date' => 'Você deve informar a data real de devolução ao marcar o empréstimo como devolvido.'
            ]);
        }

        $returnDate = Carbon::parse($data['return_date']);
        $dueDate = Carbon::parse($loan->due_date);

        if ($data['status'] === LoanStatus::RETURNED->value && $returnDate->greaterThan($dueDate)) {
            throw ValidationException::withMessages([
                'status' => 'Não é possível marcar como "Devolvido no prazo" uma devolução que está atrasada.'
            ]);
        }

        if ($data['status'] === LoanStatus::LATE->value && $returnDate->lessThanOrEqualTo($dueDate)) {
            throw ValidationException::withMessages([
                'status' => 'Não é possível marcar como "Devolvido com atraso" uma devolução dentro do prazo.'
            ]);
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


    public function delete(Loan $loan): void
    {
        DB::transaction(function () use ($loan) {
            if (empty($loan->return_date)) {
                $this->handleStockIncrement($loan->loanable, 'available');
            }
            $loan->delete();
        });
    }

    public function getOverdueLoans(): Collection
    {
        return Loan::overdue()->with(['student.person', 'loanable'])->get();
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

    private function handleStockDecrement($item): void
    {
        if (isset($item->type) && !$item->type->is_digital) {
            if ($item->quantity_available <= 0) {
                throw new \Exception('Não há unidades disponíveis em estoque.');
            }

            $item->decrement('quantity_available');

            if ($item->quantity_available <= 0) {
                $unavailableStatus = ResourceStatus::where('code', 'unavailable')->first();
                if ($unavailableStatus) {
                    $item->update(['status_id' => $unavailableStatus->id]);
                }
            }
        }
    }

    public function calculateStockForLoan($item, array $data): array
    {
        $type = ResourceType::find($data['type_id'] ?? $item->type_id);

        if ($type?->is_digital) {
            $data['quantity'] = null;
            $data['quantity_available'] = null;
            return $data;
        }

        $total = (int) ($data['quantity'] ?? $item->quantity ?? 0);

        $activeLoans = $item->exists
            ? $item->loans()->whereIn('status', [
                LoanStatus::ACTIVE->value,
                LoanStatus::LATE->value
            ])->count()
            : 0;

        $data['quantity_available'] = $total - $activeLoans;

        return $data;
    }

    public function validateStockAvailability($item, int $quantity): void
    {
        if (!isset($item->type) || $item->type->is_digital) {
            return;
        }

        $activeLoans = $item->exists
            ? $item->loans()->whereIn('status', [
                LoanStatus::ACTIVE->value,
                LoanStatus::LATE->value
            ])->count()
            : 0;

        if ($quantity < $activeLoans) {
            throw ValidationException::withMessages([
                'quantity' => "Mínimo permitido: {$activeLoans} (recurso atualmente em uso)."
            ]);
        }
    }
}
