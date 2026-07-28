<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Actions\Professionals;

use App\Domains\Auth\Application\Actions\Passwords\SendPasswordResetLinkAction;
use App\Domains\Auth\Application\Data\Passwords\SendPasswordResetLinkData;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\Professionals\CreateProfessionalData;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\People\CreatePersonDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Professionals\CreateProfessionalDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Position;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Cpf;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Phone;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Registration;
use App\Domains\SpecializedEducationalSupport\Infrastructure\Storage\ProfessionalPhotoStorage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

final readonly class CreateProfessionalAction
{
    public function __construct(
        private SendPasswordResetLinkAction $sendPasswordResetLink,
        private ProfessionalPhotoStorage $photoStorage,
    ) {}

    /**
     * @throws Throwable
     */
    public function execute(CreateProfessionalData $data, ?User $actor): Professional
    {
        $photoPath = $data->photo !== null
            ? $this->photoStorage->store($data->photo)
            : null;

        try {
            $professional = DB::transaction(function () use ($data, $actor, $photoPath): Professional {
                $position = Position::query()
                    ->lockForUpdate()
                    ->findOrFail($data->positionId);
                $position->ensureIsActive();

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

                $professionalDTO = new CreateProfessionalDTO(
                    registration: Registration::from($data->registration),
                    entryDate: $data->entryDate,
                );

                $professional = Professional::register($person, $position, $professionalDTO);
                $professional->save();

                User::query()->create([
                    'name' => $person->name,
                    'email' => $person->email,
                    'password' => Hash::make(Str::random(40)),
                    'role' => 'professional',
                    'professional_id' => $professional->getKey(),
                    'is_admin' => $actor?->isAdmin() === true && $data->isAdmin,
                ]);

                return $professional->load(['person', 'position', 'user']);
            });
        } catch (Throwable $exception) {
            $this->photoStorage->delete($photoPath);

            throw $exception;
        }

        $this->sendPasswordResetLink->execute(
            new SendPasswordResetLinkData(email: $professional->person->email),
        );

        return $professional;
    }
}
