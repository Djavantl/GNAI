<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Data\Profiles;

use App\Domains\Auth\Domain\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * Perfil das contas sem vínculo com pessoa, como o administrador superior.
 */
#[MapInputName(SnakeCaseMapper::class)]
final class UpdateAccountProfileData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public ?UploadedFile $photo = null,
        public bool $removePhoto = false,
        public ?string $currentPassword = null,
        public ?string $password = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        foreach (['name', 'email'] as $field) {
            if (array_key_exists($field, $properties) && is_string($properties[$field])) {
                $properties[$field] = trim($properties[$field]);
            }
        }

        if (array_key_exists('remove_photo', $properties)) {
            $properties['remove_photo'] = filter_var($properties['remove_photo'], FILTER_VALIDATE_BOOLEAN);
        }

        return $properties;
    }

    public static function rules(): array
    {
        $user = request()->user();

        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email')->ignore($user instanceof User ? $user->getKey() : null),
            ],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'remove_photo' => ['sometimes', 'boolean'],
            'current_password' => ['nullable', 'string'],
            // Mesmas regras de senha do administrador superior criado pelo terminal.
            'password' => [
                'nullable',
                'string',
                'confirmed',
                Password::min(12)->mixedCase()->numbers()->symbols()->uncompromised(),
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.min' => 'O nome deve ter ao menos 3 caracteres.',
            'name.max' => 'O nome não pode ultrapassar 255 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'email.max' => 'O e-mail não pode ultrapassar 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado em outra conta.',
            'photo.image' => 'A foto deve ser uma imagem.',
            'photo.mimes' => 'A foto deve estar nos formatos jpg, jpeg ou png.',
            'photo.max' => 'A foto não pode ser maior que 2MB.',
            'remove_photo.boolean' => 'O campo de remoção da foto é inválido.',
            'password.confirmed' => 'As senhas digitadas não conferem.',
            'password.min' => 'A nova senha deve ter pelo menos :min caracteres.',
            'password.mixed' => 'A nova senha deve conter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A nova senha deve conter pelo menos um número.',
            'password.symbols' => 'A nova senha deve conter pelo menos um símbolo.',
            'password.uncompromised' => 'Esta senha aparece em vazamentos conhecidos. Escolha outra senha.',
        ];
    }
}
