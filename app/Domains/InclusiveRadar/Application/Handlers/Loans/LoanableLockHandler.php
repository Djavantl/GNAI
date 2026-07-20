<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Application\Handlers\Loans;

use App\Domains\InclusiveRadar\Domain\Enums\LoanableType;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidLoanableResource;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;

final class LoanableLockHandler
{
    /**
     * @throws InvalidLoanableResource
     */
    public function lockForLoan(LoanableType $type, int $id): AccessibleEducationalMaterial|AssistiveTechnology
    {
        $model = $type->modelClass();

        /** @var AccessibleEducationalMaterial|AssistiveTechnology|null $item */
        $item = $model::query()
            ->lockForUpdate()
            ->find($id);

        if (
            ! $item instanceof AccessibleEducationalMaterial
            && ! $item instanceof AssistiveTechnology
        ) {
            throw new InvalidLoanableResource('O item para empréstimo não foi encontrado.');
        }

        return $item;
    }
}
