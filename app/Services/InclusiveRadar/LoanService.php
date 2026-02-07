<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\{AccessibleEducationalMaterial, Loan, AssistiveTechnology, ResourceStatus, ResourceType};
use App\Models\SpecializedEducationalSupport\{Professional, Student};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LoanService
{

    public function listAll(): Collection
    {
        return Loan::with(['loanable', 'student.person', 'professional.person'])
            ->orderByDesc('loan_date')
            ->get();
    }

    public function getCreateData(): array
    {
        $assistive_technologies = AssistiveTechnology::where('is_active', true)
            ->whereHas('resourceStatus', fn($q) => $q->where('blocks_loan', false))
            ->with('type')
            ->get()
            ->filter(fn($item) => $item->type?->is_digital || $item->quantity_available > 0);

        $educational_materials = AccessibleEducationalMaterial::where('is_active', true)
            ->whereHas('resourceStatus', fn($q) => $q->where('blocks_loan', false))
            ->get();

        return [
            'students'      => Student::with('person')->get()->sortBy('person.name'),
            'professionals' => Professional::with('person')->get()->sortBy('person.name'),
            'assistive_technologies' => $assistive_technologies,
            'educational_materials'  => $educational_materials,
        ];
    }

    public function getEditData(Loan $loan): array
    {
        return [
            'loan'          => $loan->load(['loanable', 'student.person', 'professional.person']),
            'students'      => Student::with('person')->get()->sortBy('person.name'),
            'professionals' => Professional::with('person')->get()->sortBy('person.name'),
        ];
    }

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

            return Loan::create($data);
        });
    }

    public function update(Loan $loan, array $data): Loan
    {
        return DB::transaction(function () use ($loan, $data) {
            $isReturningNow = empty($loan->return_date) && !empty($data['return_date']);

            if (!empty($data['return_date'])) {
                $returnDate = Carbon::parse($data['return_date']);
                $dueDate = Carbon::parse($loan->due_date);

                if (($data['status'] ?? $loan->status) === 'active') {
                    $data['status'] = $returnDate->greaterThan($dueDate) ? 'late' : 'returned';
                }
            }

            if ($isReturningNow) {
                $this->handleStockIncrement($loan->loanable, $data['status'] ?? 'returned');
            }

            $loan->update($data);
            return $loan->fresh();
        });
    }

    public function markAsReturned(Loan $loan, array $data = []): Loan
    {
        return $this->update($loan, [
            'return_date' => now(),
            'status'      => !empty($data['is_damaged']) ? 'damaged' : 'active',
            'observation' => $data['observation'] ?? $loan->observation
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

            $code = ($status === 'damaged') ? 'damaged' : 'available';
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
            ? $item->loans()->whereIn('status', ['active', 'late'])->count()
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
            ? $item->loans()->whereIn('status', ['active', 'late'])->count()
            : 0;

        if ($quantity < $activeLoans) {
            throw ValidationException::withMessages([
                'quantity' => "Mínimo permitido: {$activeLoans} (recurso atualmente em uso)."
            ]);
        }
    }


}
