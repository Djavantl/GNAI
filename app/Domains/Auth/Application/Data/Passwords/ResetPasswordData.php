<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Data\Passwords;

use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ResetPasswordData extends Data
{
    public function __construct(
        public string $token,
        public string $email,
        public string $password,
        public string $passwordConfirmation,
    ) {}

    public static function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ];
    }

    public static function messages(): array
    {
        return [
            'token.required' => 'O token de redefinição é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.confirmed' => 'As senhas digitadas não conferem.',
            'password.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
            'password.letters' => 'A nova senha deve conter letras.',
            'password.numbers' => 'A nova senha deve conter números.',
        ];
    }
}
