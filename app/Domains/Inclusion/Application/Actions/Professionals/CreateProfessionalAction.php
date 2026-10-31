<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Actions\Professionals;

use App\Domains\Auth\Application\Actions\Passwords\SendPasswordResetLinkAction;
use App\Domains\Auth\Application\Data\Passwords\SendPasswordResetLinkData;
use App\Domains\Auth\Application\Policies\AdministratorAccessPolicy;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Application\Data\Professionals\CreateProfessionalData;
use App\Domains\Inclusion\Domain\DTOs\People\CreatePersonDTO;
use App\Domains\Inclusion\Domain\DTOs\Professionals\CreateProfessionalDTO;
use App\Domains\Inclusion\Domain\Models\Person;
use App\Domains\Inclusion\Domain\Models\Position;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Domain\ValueObjects\Cpf;
use App\Domains\Inclusion\Domain\ValueObjects\Phone;
use App\Domains\Inclusion\Domain\ValueObjects\Registration;
use App\Domains\Inclusion\Infrastructure\Storage\ProfessionalPhotoStorage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

final readonly class CreateProfessionalAction
{
    public function __construct(
        private SendPasswordResetLinkAction $sendPasswordResetLink,
        private ProfessionalPhotoStorage $photoStorage,
        private AdministratorAccessPolicy $administratorAccess,
    ) {}

    /**
     * Com senha informada no cadastro, o acesso é imediato; sem ela, mantém-se
     * o envio do link de definição de senha por e-mail.
     *
     * @throws Throwable
     */
    public function execute(CreateProfessionalData $data, ?User $actor): Professional
    {
        $this->administratorAccess->ensureCanAssign($actor, null, $data->isAdmin);

        $photoPath = $data->photo !== null
            ? $this->photoStorage->store($data->photo)
            : null;

        try {
            $professional = DB::transaction(function () use ($data, $actor, $photoPath): Professional {
                $position = Position::query()
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
                $professional->showOnWebsite($data->showOnWebsite);
                $professional->save();

                User::query()->create([
                    'name' => $person->name,
                    'email' => $person->email,
                    'password' => Hash::make($data->password ?? Str::random(40)),
                    'role' => 'professional',
                    'professional_id' => $professional->getKey(),
                    'is_admin' => $data->isAdmin,
                ]);

                return $professional->load(['person', 'position', 'user']);
            });
        } catch (Throwable $exception) {
            $this->photoStorage->delete($photoPath);

            throw $exception;
        }

        if ($data->password === null) {
            $this->sendPasswordResetLink->execute(
                new SendPasswordResetLinkData(email: $professional->person->email),
            );
        }

        return $professional;
    }
}
