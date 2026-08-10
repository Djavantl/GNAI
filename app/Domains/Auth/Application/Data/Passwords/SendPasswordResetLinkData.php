<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Data\Passwords;

use Spatie\LaravelData\Data;

final class SendPasswordResetLinkData extends Data
{
    public function __construct(
        public string $email,
    ) {}

    public static function rules(): array
    {
        return [
            'email' => ['required', 'email'],
        ];
    }

    public static function messages(): array
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
        ];
    }
}
