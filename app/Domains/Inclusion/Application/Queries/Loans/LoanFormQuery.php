<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Queries\Loans;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Enums\ResourceStatus;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Loan;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Domain\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class LoanFormQuery
{
    /**
     * @return array<string, mixed>
     */
    public function forCreation(
        ?User $authUser = null,
        ?int $selectedStudentId = null,
        ?int $selectedProfessionalId = null,
        ?int $selectedItemId = null,
        ?string $selectedItemType = null,
    ): array {
        return $this->baseOptions() + [
            'itemTypes' => ItemType::options(),
            'items' => $this->loanableItems(),
            'authUser' => $authUser,
            'selectedStudentId' => $selectedStudentId,
            'selectedProfessionalId' => $selectedProfessionalId,
            'selectedItemId' => $selectedItemId,
            'selectedItemType' => $selectedItemType,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function forUpdate(Loan $loan, ?User $authUser = null): array
    {
        $loan->loadMissing([
            'loanable',
            'student:id,person_id,registration',
            'student.person:id,name',
            'professional:id,person_id,registration',
            'professional.person:id,name',
        ]);

        return $this->baseOptions() + [
            'loan' => $loan,
            'authUser' => $authUser,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function baseOptions(): array
    {
        return [
            'students' => Student::query()
                ->with('person:id,name')
                ->get(['id', 'person_id', 'registration'])
                ->sortBy('person.name')
                ->mapWithKeys(
                    static fn (Student $student): array => [
                        $student->id => trim(
                            ($student->person?->name ?? 'Sem nome')
                            .' ('.$student->registration.')',
                        ),
                    ],
                ),
            'professionals' => Professional::query()
                ->with('person:id,name')
                ->get(['id', 'person_id', 'registration'])
                ->sortBy('person.name')
                ->mapWithKeys(
                    static fn (Professional $professional): array => [
                        $professional->id => trim(
                            ($professional->person?->name ?? 'Sem nome')
                            .' - '
                            .$professional->registration,
                        ),
                    ],
                ),
        ];
    }

    /**
     * @return Collection<int, array{id: int, item_type: string, name: string, asset_code: string, is_digital: bool, quantity_available: int|null}>
     */
    private function loanableItems(): Collection
    {
        return Item::query()
            ->select([
                'id',
                'item_type',
                'name',
                'asset_code',
                'is_digital',
                'quantity_available',
            ])
            ->where('is_active', true)
            ->where('is_loanable', true)
            ->whereNotIn('status', [
                ResourceStatus::UNDER_MAINTENANCE->value,
                ResourceStatus::DAMAGED->value,
                ResourceStatus::UNAVAILABLE->value,
            ])
            ->where(function (Builder $query): void {
                $query
                    ->where('is_digital', true)
                    ->orWhere('quantity_available', '>', 0);
            })
            ->orderBy('name')
            ->get()
            ->map(
                static fn (Item $item): array => [
                    'id' => $item->id,
                    'item_type' => $item->item_type->value,
                    'name' => $item->name,
                    'asset_code' => $item->asset_code ?? 'S/N',
                    'is_digital' => $item->is_digital,
                    'quantity_available' => $item->quantity_available,
                ],
            );
    }
}
