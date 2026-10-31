<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Rules;

use App\Domains\Inclusion\Domain\Exceptions\InvalidCpf;
use App\Domains\Inclusion\Domain\ValueObjects\Cpf;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

final class ValidCpf implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            Cpf::fromNullable((string) $value);
        } catch (InvalidCpf) {
            $fail('O CPF informado é inválido.');
        }
    }
}
