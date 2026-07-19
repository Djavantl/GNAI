<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Enums;

use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidLoanableResource;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;

enum LoanableType: string
{
    case AssistiveTechnology = 'assistive_technology';
    case AccessibleEducationalMaterial = 'accessible_educational_material';

    /**
     * @return class-string<AccessibleEducationalMaterial|AssistiveTechnology>
     */
    public function modelClass(): string
    {
        return match ($this) {
            self::AssistiveTechnology => AssistiveTechnology::class,
            self::AccessibleEducationalMaterial => AccessibleEducationalMaterial::class,
        };
    }

    public static function fromModel(AccessibleEducationalMaterial|AssistiveTechnology $item): self
    {
        return match (true) {
            $item instanceof AssistiveTechnology => self::AssistiveTechnology,
            $item instanceof AccessibleEducationalMaterial => self::AccessibleEducationalMaterial,
        };
    }

    public static function fromStored(string $value): self
    {
        return self::tryFrom($value)
            ?? throw new InvalidLoanableResource('O tipo de item selecionado é inválido.');
    }
}
