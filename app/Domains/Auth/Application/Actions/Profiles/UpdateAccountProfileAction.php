<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Actions\Profiles;

use App\Domains\Auth\Application\Data\Profiles\UpdateAccountProfileData;
use App\Domains\Auth\Domain\Exceptions\InvalidProfile;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Auth\Infrastructure\Storage\ProfilePhotoStorage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

final readonly class UpdateAccountProfileAction
{
    public function __construct(
        private ProfilePhotoStorage $photoStorage,
    ) {}

    /**
     * @throws InvalidProfile
     * @throws ValidationException
     * @throws Throwable
     */
    public function execute(User $user, UpdateAccountProfileData $data): void
    {
        if ($user->hasPersonProfile()) {
            throw new InvalidProfile('Seus dados pessoais são atualizados pelo perfil vinculado à equipe.');
        }

        $changesEmail = mb_strtolower($data->email) !== mb_strtolower((string) $user->email);
        $changesPassword = filled($data->password);

        // E-mail e senha são as credenciais de acesso: exigem a senha atual.
        if (($changesEmail || $changesPassword) && ! Hash::check((string) $data->currentPassword, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => filled($data->currentPassword)
                    ? 'A senha atual está incorreta.'
                    : 'Informe sua senha atual para alterar o e-mail ou a senha.',
            ]);
        }

        $oldPhoto = $user->photo;
        $newPhoto = $oldPhoto;

        if ($data->photo !== null) {
            $newPhoto = $this->photoStorage->store($data->photo);
        } elseif ($data->removePhoto) {
            $newPhoto = null;
        }

        $attributes = [
            'name' => $data->name,
            'email' => $data->email,
            'photo' => $newPhoto,
        ];

        if ($changesPassword) {
            $attributes['password'] = Hash::make((string) $data->password);
            $attributes['remember_token'] = Str::random(60);
        }

        try {
            $user->forceFill($attributes)->save();
        } catch (Throwable $exception) {
            if ($newPhoto !== $oldPhoto) {
                $this->photoStorage->delete($newPhoto);
            }

            throw $exception;
        }

        if ($oldPhoto !== $newPhoto) {
            $this->photoStorage->delete($oldPhoto);
        }
    }
}
