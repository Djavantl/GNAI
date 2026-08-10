<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Professionals;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Professionals\PersonHasOtherRolesQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Professionals\ProfessionalHasLinkedRecordsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Storage\ProfessionalPhotoStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class DeleteProfessionalAction
{
    public function __construct(
        private ProfessionalHasLinkedRecordsQuery $hasLinkedRecords,
        private PersonHasOtherRolesQuery $hasOtherRoles,
        private ProfessionalPhotoStorage $photoStorage,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Professional $professional, ?User $actor): void
    {
        $photoPath = DB::transaction(function () use ($professional, $actor): ?string {
            $lockedProfessional = Professional::query()
                ->lockForUpdate()
                ->findOrFail($professional->getKey());
            $lockedProfessional->ensureCanBeDeletedBy(
                $actor?->professional_id === null ? null : (int) $actor->professional_id,
            );
            $lockedProfessional->ensureCanBeDeleted(
                $this->hasLinkedRecords->execute($lockedProfessional),
            );

            $person = Person::query()
                ->lockForUpdate()
                ->findOrFail($lockedProfessional->person_id);
            $personHasOtherRoles = $this->hasOtherRoles->execute($person, $lockedProfessional);

            $lockedProfessional->user()->delete();
            $lockedProfessional->delete();

            if ($personHasOtherRoles) {
                return null;
            }

            $photoPath = $person->getRawOriginal('photo');
            $person->delete();

            return $photoPath;
        });

        $this->photoStorage->delete($photoPath);
    }
}
