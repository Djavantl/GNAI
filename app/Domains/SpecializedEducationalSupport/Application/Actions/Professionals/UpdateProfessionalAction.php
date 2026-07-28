<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Professionals;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\Professionals\UpdateProfessionalData;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Professionals\ProfessionalHasPendingPendenciesQuery;
use App\Domains\SpecializedEducationalSupport\Application\Queries\Professionals\ProfessionalHasSessionsQuery;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\People\UpdatePersonDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Professionals\UpdateProfessionalDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\ProfessionalStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Position;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Cpf;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Phone;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Registration;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Storage\ProfessionalPhotoStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateProfessionalAction
{
    public function __construct(
        private ProfessionalHasPendingPendenciesQuery $hasPendingPendencies,
        private ProfessionalHasSessionsQuery $hasSessions,
        private ProfessionalPhotoStorage $photoStorage,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Professional $professional, UpdateProfessionalData $data, ?User $actor): Professional
    {
        $newPhotoPath = $data->photo !== null
            ? $this->photoStorage->store($data->photo)
            : null;
        $oldPhotoPath = null;

        try {
            $updatedProfessional = DB::transaction(function () use ($professional, $data, $actor, $newPhotoPath, &$oldPhotoPath): Professional {
                $lockedProfessional = Professional::query()
                    ->lockForUpdate()
                    ->findOrFail($professional->getKey());
                $position = Position::query()
                    ->lockForUpdate()
                    ->findOrFail($data->positionId);
                $position->ensureIsActive();
                $person = Person::query()
                    ->lockForUpdate()
                    ->findOrFail($lockedProfessional->person_id);
                $currentPhotoPath = $person->getRawOriginal('photo');

                if ($lockedProfessional->status !== $data->status && $data->status === ProfessionalStatus::INACTIVE) {
                    $lockedProfessional->ensureCanBeInactivated(
                        hasPendingPendencies: $this->hasPendingPendencies->execute($lockedProfessional),
                        hasSessions: $this->hasSessions->execute($lockedProfessional),
                    );
                }

                if ($newPhotoPath !== null) {
                    $photoPath = $newPhotoPath;
                    $oldPhotoPath = $currentPhotoPath;
                } elseif ($data->removePhoto) {
                    $photoPath = null;
                    $oldPhotoPath = $currentPhotoPath;
                } else {
                    $photoPath = $currentPhotoPath;
                }

                $personDTO = new UpdatePersonDTO(
                    name: $data->name,
                    birthDate: $data->birthDate,
                    gender: $data->gender,
                    document: Cpf::fromNullable($data->document),
                    email: $data->email,
                    phone: Phone::fromNullable($data->phone),
                    address: $data->address,
                    photo: $photoPath,
                );

                $person->revise($personDTO);
                $person->save();

                $professionalDTO = new UpdateProfessionalDTO(
                    registration: Registration::from($data->registration),
                    status: $data->status,
                    entryDate: $data->entryDate,
                );

                $lockedProfessional->revise($position, $professionalDTO);
                $lockedProfessional->save();

                $user = $lockedProfessional->user()->lockForUpdate()->first();

                if ($user !== null) {
                    $userData = [
                        'name' => $person->name,
                        'email' => $person->email,
                    ];

                    if ($actor?->isAdmin() === true) {
                        $userData['is_admin'] = $data->isAdmin;
                    }

                    $user->update($userData);
                }

                return $lockedProfessional->load(['person', 'position', 'user']);
            });
        } catch (Throwable $exception) {
            $this->photoStorage->delete($newPhotoPath);

            throw $exception;
        }

        if ($oldPhotoPath !== null && $oldPhotoPath !== $newPhotoPath) {
            $this->photoStorage->delete($oldPhotoPath);
        }

        return $updatedProfessional;
    }
}
