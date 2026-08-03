<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Sessions;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\Sessions\ListSessionsData;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class ListMySessionsQuery
{
    public function execute(ListSessionsData $filters, User $user): LengthAwarePaginator
    {
        $professional = $user->professional;

        abort_unless($professional !== null, 403, 'Acesso permitido apenas para profissionais.');

        return Session::query()
            ->with(['students.person', 'professional.person', 'aeeRecord', 'pedagogicalRecord'])
            ->where('professional_id', $professional->id)
            ->when($filters->student !== null, function ($query) use ($filters): void {
                $query->whereHas('students', fn ($studentQuery) => $studentQuery->where('students.id', $filters->student));
            })
            ->when($filters->type !== null && $filters->type !== '', fn ($query) => $query->where('type', $filters->type))
            ->when($filters->status !== null && $filters->status !== '', fn ($query) => $query->where('status', $filters->status))
            ->orderByDesc('session_date')
            ->paginate(10)
            ->withQueryString();
    }
}
