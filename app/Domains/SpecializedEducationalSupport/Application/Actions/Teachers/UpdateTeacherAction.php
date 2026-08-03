<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Teachers;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\Teachers\UpdateTeacherData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\People\UpdatePersonDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Teachers\UpdateTeacherDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Cpf;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Phone;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Registration;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Storage\TeacherPhotoStorage;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class UpdateTeacherAction
{
    public function __construct(
        private TeacherPhotoStorage $photoStorage,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(Teacher $teacher, UpdateTeacherData $data): Teacher
    {
        $newPhotoPath = $data->photo !== null
            ? $this->photoStorage->store($data->photo)
            : null;
        $oldPhotoPath = null;

        try {
            $updatedTeacher = DB::transaction(function () use ($teacher, $data, $newPhotoPath, &$oldPhotoPath): Teacher {
                $lockedTeacher = Teacher::query()
                    ->with('person')
                    ->lockForUpdate()
                    ->findOrFail($teacher->getKey());

                $person = $lockedTeacher->person;
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
                    gender: Gender::from($data->gender),
                    document: Cpf::fromNullable($data->document),
                    email: $data->email,
                    phone: Phone::fromNullable($data->phone),
                    address: $data->address,
                    photo: $photoPath,
                );

                $person->revise($personDTO);
                $person->save();

                $teacherDTO = new UpdateTeacherDTO(
                    registration: Registration::from($data->registration),
                );

                $lockedTeacher->revise($teacherDTO);
                $lockedTeacher->save();

                User::query()
                    ->where('teacher_id', $lockedTeacher->id)
                    ->update([
                        'name' => $person->name,
                        'email' => $person->email,
                    ]);

                return $lockedTeacher->fresh('person');
            });
        } catch (Throwable $exception) {
            $this->photoStorage->delete($newPhotoPath);

            throw $exception;
        }

        if ($oldPhotoPath !== null && $oldPhotoPath !== $newPhotoPath) {
            $this->photoStorage->delete($oldPhotoPath);
        }

        return $updatedTeacher;
    }
}
