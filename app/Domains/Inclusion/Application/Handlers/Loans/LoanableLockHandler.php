<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Handlers\Loans;

use App\Domains\Inclusion\Domain\Enums\ItemType;
use App\Domains\Inclusion\Domain\Exceptions\InvalidLoanableResource;
use App\Domains\Inclusion\Domain\Models\Item;

final class LoanableLockHandler
{
    /**
     * @throws InvalidLoanableResource
     */
    public function lockForLoan(int $id, ?ItemType $expectedType = null): Item
    {
        $item = Item::query()
            ->lockForUpdate()
            ->find($id);

        if (! $item instanceof Item) {
            throw new InvalidLoanableResource('O item para empréstimo não foi encontrado.');
        }

        if ($expectedType !== null && $item->item_type !== $expectedType) {
            throw new InvalidLoanableResource('O item selecionado não corresponde ao tipo informado.');
        }

        return $item;
    }
}
