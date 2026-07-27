<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Rules;

use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidCpf;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Cpf;
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
