<?php

declare(strict_types=1);

namespace App\Domains\Settings\Application\Data;

use Spatie\LaravelData\Data;

final class SendTestMailData extends Data
{
    public function __construct(
        public string $recipient,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        if (array_key_exists('recipient', $properties) && is_string($properties['recipient'])) {
            $properties['recipient'] = trim($properties['recipient']);
        }

        return $properties;
    }

    public static function rules(): array
    {
        return [
            'recipient' => ['required', 'string', 'email:rfc', 'max:255'],
        ];
    }

    public static function messages(): array
    {
        return [
            'recipient.required' => 'Informe o e-mail de destino do teste.',
            'recipient.email' => 'Informe um e-mail de destino válido.',
            'recipient.max' => 'O e-mail de destino não pode ultrapassar 255 caracteres.',
        ];
    }
}
