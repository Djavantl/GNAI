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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

final readonly class UpdateTeacherAction
{
    /**
     * @throws Throwable
     */
    public function execute(Teacher $teacher, UpdateTeacherData $data): Teacher
    {
        $newPhoto = $data->photo?->store('photos', 'public');
        $oldPhoto = null;

        try {
            $updatedTeacher = DB::transaction(function () use ($teacher, $data, $newPhoto, &$oldPhoto): Teacher {
                $lockedTeacher = Teacher::query()
                    ->with('person')
                    ->lockForUpdate()
                    ->findOrFail($teacher->getKey());

                $person = $lockedTeacher->person;
                $photo = $person->photo;
                $oldPhoto = $person->photo;

                if ($newPhoto !== null) {
                    $photo = $newPhoto;
                } elseif ($data->removePhoto) {
                    $photo = null;
                }

                $personDTO = new UpdatePersonDTO(
                    name: $data->name,
                    birthDate: $data->birthDate,
                    gender: Gender::from($data->gender),
                    document: Cpf::fromNullable($data->document),
                    email: $data->email,
                    phone: Phone::fromNullable($data->phone),
                    address: $data->address,
                    photo: $photo,
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
            if ($newPhoto !== null) {
                Storage::disk('public')->delete($newPhoto);
            }

            throw $exception;
        }

        if ($oldPhoto !== null && ($newPhoto !== null || $data->removePhoto)) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return $updatedTeacher;
    }
}
