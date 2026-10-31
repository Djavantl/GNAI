<?php

declare(strict_types=1);

namespace App\Domains\Settings\Application\Data;

use App\Domains\Settings\Domain\Enums\MailEncryption;
use App\Domains\Settings\Domain\Enums\MailTransport;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class UpdateMailSettingsData extends Data
{
    public function __construct(
        public MailTransport $transport,
        public MailEncryption $encryption,
        public string $fromAddress,
        public string $fromName,
        public ?string $host = null,
        public ?int $port = null,
        public ?string $username = null,
        public ?string $password = null,
    ) {}

    public static function prepareForPipeline(array $properties): array
    {
        foreach (['host', 'username', 'from_address', 'from_name'] as $field) {
            if (array_key_exists($field, $properties) && is_string($properties[$field])) {
                $properties[$field] = trim($properties[$field]) === '' ? null : trim($properties[$field]);
            }
        }

        if (array_key_exists('password', $properties) && $properties['password'] === '') {
            $properties['password'] = null;
        }

        if (array_key_exists('port', $properties)) {
            $properties['port'] = is_numeric($properties['port']) ? (int) $properties['port'] : null;
        }

        return $properties;
    }

    public static function rules(): array
    {
        return [
            'transport' => ['required', Rule::enum(MailTransport::class)],
            'host' => ['nullable', 'required_if:transport,smtp', 'string', 'max:255'],
            'port' => ['nullable', 'required_if:transport,smtp', 'integer', 'between:1,65535'],
            'encryption' => ['required', Rule::enum(MailEncryption::class)],
            'username' => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'max:255'],
            'from_address' => ['required', 'string', 'email:rfc', 'max:255'],
            'from_name' => ['required', 'string', 'max:255'],
        ];
    }

    public static function messages(): array
    {
        return [
            'transport.required' => 'Informe o tipo de envio.',
            'transport.enum' => 'O tipo de envio selecionado é inválido.',
            'host.required_if' => 'Informe o servidor SMTP.',
            'host.max' => 'O servidor SMTP não pode ultrapassar 255 caracteres.',
            'port.required_if' => 'Informe a porta do servidor SMTP.',
            'port.integer' => 'A porta deve ser um número inteiro.',
            'port.between' => 'A porta deve estar entre 1 e 65535.',
            'encryption.required' => 'Informe o tipo de segurança da conexão.',
            'encryption.enum' => 'O tipo de segurança selecionado é inválido.',
            'username.max' => 'O usuário não pode ultrapassar 255 caracteres.',
            'password.max' => 'A senha não pode ultrapassar 255 caracteres.',
            'from_address.required' => 'Informe o e-mail de envio.',
            'from_address.email' => 'Informe um e-mail de envio válido.',
            'from_address.max' => 'O e-mail de envio não pode ultrapassar 255 caracteres.',
            'from_name.required' => 'Informe o nome do remetente.',
            'from_name.max' => 'O nome do remetente não pode ultrapassar 255 caracteres.',
        ];
    }
}
