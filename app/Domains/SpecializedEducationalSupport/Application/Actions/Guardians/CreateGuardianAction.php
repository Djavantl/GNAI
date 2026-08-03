<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Guardians;

use App\Domains\SpecializedEducationalSupport\Application\Data\Guardians\CreateGuardianData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Guardians\CreateGuardianDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\People\CreatePersonDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Guardian;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Cpf;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Phone;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Storage\GuardianPhotoStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateGuardianAction
{
    public function __construct(
        private GuardianPhotoStorage $photoStorage,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Student $student, CreateGuardianData $data): Guardian
    {
        $photoPath = $data->photo !== null
            ? $this->photoStorage->store($data->photo)
            : null;

        try {
            return DB::transaction(function () use ($student, $data, $photoPath): Guardian {
                $lockedStudent = Student::query()
                    ->lockForUpdate()
                    ->findOrFail($student->getKey());
                $lockedStudent->ensureIsActive();

                $personDTO = new CreatePersonDTO(
                    name: $data->name,
                    birthDate: $data->birthDate,
                    gender: $data->gender,
                    document: Cpf::fromNullable($data->document),
                    email: $data->email,
                    phone: Phone::from($data->phone),
                    address: $data->address,
                    photo: $photoPath,
                );

                $person = Person::register($personDTO);
                $person->save();

                $guardianDTO = new CreateGuardianDTO(
                    relationship: $data->relationship,
                );

                $guardian = Guardian::register($lockedStudent, $person, $guardianDTO);
                $guardian->save();

                return $guardian->load(['person', 'student.person']);
            });
        } catch (Throwable $exception) {
            $this->photoStorage->delete($photoPath);

            throw $exception;
        }
    }
}
