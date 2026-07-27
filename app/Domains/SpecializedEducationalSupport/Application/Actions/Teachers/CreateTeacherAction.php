<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Teachers;

use App\Domains\Auth\Application\Actions\Passwords\SendPasswordResetLinkAction;
use App\Domains\Auth\Application\Data\Passwords\SendPasswordResetLinkData;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\Teachers\CreateTeacherData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\People\CreatePersonDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Teachers\CreateTeacherDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Cpf;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Phone;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Registration;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Storage\TeacherPhotoStorage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

final readonly class CreateTeacherAction
{
    public function __construct(
        private SendPasswordResetLinkAction $sendPasswordResetLink,
        private TeacherPhotoStorage $photoStorage,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(CreateTeacherData $data): Teacher
    {
        $photoPath = $data->photo !== null
            ? $this->photoStorage->store($data->photo)
            : null;

        try {
            $teacher = DB::transaction(function () use ($data, $photoPath): Teacher {
                $personDTO = new CreatePersonDTO(
                    name: $data->name,
                    birthDate: $data->birthDate,
                    gender: Gender::from($data->gender),
                    document: Cpf::fromNullable($data->document),
                    email: $data->email,
                    phone: Phone::fromNullable($data->phone),
                    address: $data->address,
                    photo: $photoPath,
                );

                $person = Person::register($personDTO);
                $person->save();

                $teacherDTO = new CreateTeacherDTO(
                    personId: (int) $person->id,
                    registration: Registration::from($data->registration),
                );

                $teacher = Teacher::register($teacherDTO);
                $teacher->save();

                User::query()->create([
                    'name' => $person->name,
                    'email' => $person->email,
                    'password' => Hash::make(Str::random(40)),
                    'teacher_id' => $teacher->id,
                    'is_admin' => false,
                ]);

                return $teacher->load('person');
            });
        } catch (Throwable $exception) {
            $this->photoStorage->delete($photoPath);

            throw $exception;
        }

        $this->sendPasswordResetLink->execute(
            new SendPasswordResetLinkData(email: $teacher->person->email),
        );

        return $teacher;
    }
}
