<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Students;

use App\Domains\SpecializedEducationalSupport\Application\Data\Students\CreateStudentData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\People\CreatePersonDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Students\CreateStudentDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Cpf;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Phone;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Registration;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Storage\StudentPhotoStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CreateStudentAction
{
    public function __construct(
        private StudentPhotoStorage $photoStorage,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(CreateStudentData $data): Student
    {
        $photoPath = $data->photo !== null
            ? $this->photoStorage->store($data->photo)
            : null;

        try {
            return DB::transaction(function () use ($data, $photoPath): Student {
                $personDTO = new CreatePersonDTO(
                    name: $data->name,
                    birthDate: $data->birthDate,
                    gender: $data->gender,
                    document: Cpf::fromNullable($data->document),
                    email: $data->email,
                    phone: Phone::fromNullable($data->phone),
                    address: $data->address,
                    photo: $photoPath,
                );

                $person = Person::register($personDTO);
                $person->save();

                $studentDTO = new CreateStudentDTO(
                    registration: Registration::from($data->registration),
                    entryDate: $data->entryDate,
                    isRepeater: $data->isRepeater,
                );

                $student = Student::register($person, $studentDTO);
                $student->save();

                return $student->load('person');
            });
        } catch (Throwable $exception) {
            $this->photoStorage->delete($photoPath);

            throw $exception;
        }
    }
}
