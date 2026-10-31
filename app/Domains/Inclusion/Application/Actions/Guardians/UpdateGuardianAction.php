<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Guardians;

use App\Domains\Inclusion\Application\Data\Guardians\UpdateGuardianData;
use App\Domains\Inclusion\Domain\DTOs\Guardians\UpdateGuardianDTO;
use App\Domains\Inclusion\Domain\DTOs\People\UpdatePersonDTO;
use App\Domains\Inclusion\Domain\Models\Guardian;
use App\Domains\Inclusion\Domain\Models\Person;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\ValueObjects\Cpf;
use App\Domains\Inclusion\Domain\ValueObjects\Phone;
use App\Domains\Inclusion\Infrastructure\Storage\GuardianPhotoStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateGuardianAction
{
    public function __construct(
        private GuardianPhotoStorage $photoStorage,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Student $student, Guardian $guardian, UpdateGuardianData $data): Guardian
    {
        $newPhotoPath = $data->photo !== null
            ? $this->photoStorage->store($data->photo)
            : null;
        $oldPhotoPath = null;

        try {
            $updatedGuardian = DB::transaction(function () use ($student, $guardian, $data, $newPhotoPath, &$oldPhotoPath): Guardian {
                $lockedStudent = Student::query()
                    ->lockForUpdate()
                    ->findOrFail($student->getKey());
                $lockedStudent->ensureIsActive();

                $lockedGuardian = Guardian::query()
                    ->lockForUpdate()
                    ->findOrFail($guardian->getKey());
                $lockedGuardian->ensureBelongsTo($lockedStudent);

                $person = Person::query()
                    ->lockForUpdate()
                    ->findOrFail($lockedGuardian->person_id);
                $currentPhotoPath = $person->getRawOriginal('photo');

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
                    phone: Phone::from($data->phone),
                    address: $data->address,
                    photo: $photoPath,
                );

                $person->revise($personDTO);
                $person->save();

                $guardianDTO = new UpdateGuardianDTO(
                    relationship: $data->relationship,
                );

                $lockedGuardian->revise($guardianDTO);
                $lockedGuardian->save();

                return $lockedGuardian->load(['person', 'student.person']);
            });
        } catch (Throwable $exception) {
            $this->photoStorage->delete($newPhotoPath);

            throw $exception;
        }

        if ($oldPhotoPath !== null && $oldPhotoPath !== $newPhotoPath) {
            $this->photoStorage->delete($oldPhotoPath);
        }

        return $updatedGuardian;
    }
}
