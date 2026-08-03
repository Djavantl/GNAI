<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Students;

use App\Domains\SpecializedEducationalSupport\Application\Data\Students\UpdateStudentData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\People\UpdatePersonDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Students\UpdateStudentDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Cpf;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Phone;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Registration;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Storage\StudentPhotoStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateStudentAction
{
    public function __construct(
        private StudentPhotoStorage $photoStorage,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Student $student, UpdateStudentData $data): Student
    {
        $newPhotoPath = $data->photo !== null
            ? $this->photoStorage->store($data->photo)
            : null;
        $oldPhotoPath = null;

        try {
            $updatedStudent = DB::transaction(function () use ($student, $data, $newPhotoPath, &$oldPhotoPath): Student {
                $lockedStudent = Student::query()
                    ->lockForUpdate()
                    ->findOrFail($student->getKey());
                $person = Person::query()
                    ->lockForUpdate()
                    ->findOrFail($lockedStudent->person_id);
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
                    phone: Phone::fromNullable($data->phone),
                    address: $data->address,
                    photo: $photoPath,
                );

                $person->revise($personDTO);
                $person->save();

                $studentDTO = new UpdateStudentDTO(
                    registration: Registration::from($data->registration),
                    status: $data->status,
                    entryDate: $data->entryDate,
                    isRepeater: $data->isRepeater,
                );

                $lockedStudent->revise($studentDTO);
                $lockedStudent->save();

                return $lockedStudent->load('person');
            });
        } catch (Throwable $exception) {
            $this->photoStorage->delete($newPhotoPath);

            throw $exception;
        }

        if ($oldPhotoPath !== null && $oldPhotoPath !== $newPhotoPath) {
            $this->photoStorage->delete($oldPhotoPath);
        }

        return $updatedStudent;
    }
}
