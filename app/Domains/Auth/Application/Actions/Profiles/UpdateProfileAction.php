<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Actions\Profiles;

use App\Domains\Auth\Application\Data\Profiles\UpdateProfileData;
use App\Domains\Auth\Domain\DTOs\Profiles\UpdateProfileDTO;
use App\Domains\Auth\Domain\Exceptions\InvalidProfile;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Auth\Infrastructure\Storage\ProfilePhotoStorage;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

final readonly class UpdateProfileAction
{
    public function __construct(
        private ProfilePhotoStorage $photoStorage,
    ) {}

    /**
     * @throws InvalidProfile
     * @throws Throwable
     */
    public function execute(User $user, UpdateProfileData $data): void
    {
        if ($user->is_admin && ! $user->professional_id) {
            throw new InvalidProfile(
                'Administradores sem vínculo profissional não possuem perfil de dados pessoais para edição.'
            );
        }

        $user->loadMissing(['professional.person', 'teacher.person']);

        $person = $user->professional?->person ?? $user->teacher?->person;

        if (! $person instanceof Person) {
            throw new InvalidProfile('Vínculo de pessoa não encontrado para este usuário.');
        }

        $dto = new UpdateProfileDTO(
            name: $data->name,
            registration: $data->registration,
            birthDate: $data->birthDate,
            gender: $data->gender,
            email: $data->email,
            document: $data->document,
            phone: $data->phone,
            address: $data->address,
            password: $data->password,
        );

        $oldPhoto = $person->photo;
        $newPhoto = $person->photo;

        if ($data->photo !== null) {
            $newPhoto = $this->photoStorage->store($data->photo);
        } elseif ($data->removePhoto) {
            $newPhoto = null;
        }

        try {
            DB::transaction(function () use ($user, $person, $dto, $newPhoto): void {
                $person->fill([
                    'name' => $dto->name,
                    'document' => $dto->document,
                    'birth_date' => $dto->birthDate,
                    'gender' => $dto->gender->value,
                    'phone' => $dto->phone,
                    'email' => $dto->email,
                    'address' => $dto->address,
                    'photo' => $newPhoto,
                ]);
                $person->save();

                if ($user->professional) {
                    $user->professional->update([
                        'registration' => $dto->registration,
                    ]);
                } elseif ($user->teacher) {
                    $user->teacher->update([
                        'registration' => $dto->registration,
                    ]);
                }

                $user->fill([
                    'name' => $dto->name,
                    'email' => $dto->email,
                ]);

                if (filled($dto->password)) {
                    $user->password = Hash::make((string) $dto->password);
                }

                $user->save();
            });
        } catch (Throwable $exception) {
            if ($data->photo !== null && filled($newPhoto)) {
                $this->photoStorage->delete($newPhoto);
            }

            throw $exception;
        }

        if ($oldPhoto !== $newPhoto && filled($oldPhoto)) {
            $this->photoStorage->delete($oldPhoto);
        }
    }
}
