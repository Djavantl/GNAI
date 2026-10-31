<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Handlers\Loans;

use App\Domains\Inclusion\Domain\Enums\LoanStatus;
use App\Domains\Inclusion\Domain\Enums\ResourceStatus;
use App\Domains\Inclusion\Domain\Exceptions\InsufficientStock;
use App\Domains\Inclusion\Domain\Exceptions\StockAlreadyFull;
use App\Domains\Inclusion\Domain\Models\Item;

final class LoanStockHandler
{
    /**
     * @throws InsufficientStock
     */
    public function withdraw(Item $item): void
    {
        if ($item->is_digital) {
            return;
        }

        $stock = $item->stock()->withdrawOne();

        $item->update([
            'quantity_available' => $stock->available(),
            'status' => $stock->isEmpty()
                ? ResourceStatus::IN_USE
                : $item->status,
        ]);
    }

    /**
     * @throws StockAlreadyFull
     */
    public function returnAvailable(Item $item): void
    {
        $this->returnWithStatus($item, ResourceStatus::AVAILABLE);
    }

    /**
     * @throws StockAlreadyFull
     */
    public function returnFromLoanStatus(
        Item $item,
        LoanStatus $status,
    ): void {
        $this->returnWithStatus(
            item: $item,
            status: $status === LoanStatus::DAMAGED
                ? ResourceStatus::DAMAGED
                : ResourceStatus::AVAILABLE,
        );
    }

    /**
     * @throws StockAlreadyFull
     */
    private function returnWithStatus(
        Item $item,
        ResourceStatus $status,
    ): void {
        if ($item->is_digital) {
            return;
        }

        $stock = $item->stock()->returnOne();

        $item->update([
            'quantity_available' => $stock->available(),
            'status' => $status,
        ]);
    }
}
