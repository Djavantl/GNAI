<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\WaitlistStatus;
use App\Models\InclusiveRadar\Loan;
use App\Models\InclusiveRadar\Waitlist;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WaitlistService
{
    /*
    |----------------------------------------------------------------------
    | CRUD
    |----------------------------------------------------------------------
    */

    public function store(array $data): Waitlist
    {
        return DB::transaction(function () use ($data) {
            $item = $data['waitlistable_type']::lockForUpdate()
                ->findOrFail($data['waitlistable_id']);

            $data['waitlistable_type'] = $item->getMorphClass();

            $this->validateNewWaitlist($item, $data);

            return Waitlist::create([
                'waitlistable_id'   => $item->id,
                'waitlistable_type' => $data['waitlistable_type'],
                'student_id'        => $data['student_id'] ?? null,
                'professional_id'   => $data['professional_id'] ?? null,
                'user_id'           => $data['user_id'],
                'requested_at'      => now(),
                'status'            => WaitlistStatus::WAITING->value,
                'observation'       => $data['observation'] ?? null,
            ]);
        });
    }

    public function update(Waitlist $waitlist, array $data): Waitlist
    {
        $this->validateStatusModification($waitlist, $data);

        $waitlist->update($this->filterUpdatableFields($data));

        return $waitlist->fresh();
    }

    public function delete(Waitlist $waitlist): void
    {
        $this->validateDeletion($waitlist);
        $waitlist->delete();
    }

    /*
    |----------------------------------------------------------------------
    | Business Actions
    |----------------------------------------------------------------------
    */

    public function cancel(Waitlist $waitlist): Waitlist
    {
        $currentStatus = WaitlistStatus::tryFrom($waitlist->status);

        if ($currentStatus !== WaitlistStatus::WAITING) {
            throw ValidationException::withMessages([
                'status' => 'Apenas solicitações em espera podem ser canceladas.'
            ]);
        }

        $waitlist->update(['status' => WaitlistStatus::CANCELLED->value]);

        return $waitlist->fresh();
    }

    public function notifyNext($item): ?Waitlist
    {
        $next = Waitlist::where('waitlistable_id', $item->id)
            ->where('waitlistable_type', $item->getMorphClass())
            ->where('status', WaitlistStatus::WAITING->value)
            ->oldest('requested_at')
            ->first();

        if (!$next) return null;

        $next->update(['status' => WaitlistStatus::NOTIFIED->value]);

        return $next->fresh();
    }

    public function fulfill(Waitlist $waitlist): Waitlist
    {
        $waitlist->update(['status' => WaitlistStatus::FULFILLED->value]);

        return $waitlist->fresh();
    }

    /*
    |----------------------------------------------------------------------
    | VALIDAÇÕES
    |----------------------------------------------------------------------
    */

    private function validateNewWaitlist($item, array $data): void
    {
        $this->ensureNoStockAvailable($item);
        $this->ensureNoDuplicateEntry($item, $data);
    }

    private function ensureNoStockAvailable($item): void
    {
        if ($item->quantity_available > 0) {
            throw ValidationException::withMessages([
                'waitlistable_id' => 'Este recurso ainda possui unidades disponíveis.'
            ]);
        }
    }

    private function ensureNoDuplicateEntry($item, array $data): void
    {
        $student = $data['student_id'] ?? null;
        $professional = $data['professional_id'] ?? null;

        $existsQuery = Waitlist::where('waitlistable_id', $item->id)
            ->where('waitlistable_type', $item->getMorphClass())
            ->whereIn('status', [
                WaitlistStatus::WAITING->value,
                WaitlistStatus::NOTIFIED->value
            ]);

        if ($student) $existsQuery->where('student_id', $student);
        else $existsQuery->where('professional_id', $professional);

        if ($existsQuery->exists()) {
            throw ValidationException::withMessages([
                $student ? 'student_id' : 'professional_id' =>
                    'Este beneficiário já possui uma solicitação ativa para este recurso.'
            ]);
        }

        $loanQuery = Loan::where('loanable_id', $item->id)
            ->where('loanable_type', $item->getMorphClass())
            ->whereNull('return_date');

        if ($student) $loanQuery->where('student_id', $student);
        else $loanQuery->where('professional_id', $professional);

        if ($loanQuery->exists()) {
            throw ValidationException::withMessages([
                $student ? 'student_id' : 'professional_id' =>
                    'Este beneficiário já possui um empréstimo ativo deste recurso.'
            ]);
        }
    }

    private function validateStatusModification(Waitlist $waitlist, array $data): void
    {
        if (!isset($data['status'])) return;

        $currentStatus = WaitlistStatus::tryFrom($waitlist->status);

        $updatableKeys = array_keys($data);
        $onlyObservation = count($updatableKeys) === 1 && in_array('observation', $updatableKeys);

        if (!$onlyObservation && in_array($currentStatus, [WaitlistStatus::FULFILLED, WaitlistStatus::CANCELLED], true)) {
            throw ValidationException::withMessages([
                'status' => 'Solicitação já finalizada não pode ser alterada, exceto observações.'
            ]);
        }
    }

    private function validateDeletion(Waitlist $waitlist): void
    {
        $currentStatus = WaitlistStatus::tryFrom($waitlist->status);

        if ($currentStatus === WaitlistStatus::FULFILLED) {
            throw ValidationException::withMessages([
                'status' => 'Solicitações já atendidas não podem ser removidas.'
            ]);
        }
    }

    /*
    |----------------------------------------------------------------------
    | AUXILIARES
    |----------------------------------------------------------------------
    */

    private function filterUpdatableFields(array $data): array
    {
        return collect($data)
            ->only(['status', 'observation'])
            ->toArray();
    }
}
