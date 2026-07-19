<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Domain\ValueObjects;

use App\Domains\InclusiveRadar\Domain\Exceptions\InsufficientStock;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidStock;
use App\Domains\InclusiveRadar\Domain\Exceptions\StockAlreadyFull;
use App\Domains\InclusiveRadar\Domain\ValueObjects\Stock;
use PHPUnit\Framework\TestCase;

final class StockTest extends TestCase
{
    public function test_it_creates_initial_stock_with_every_unit_available(): void
    {
        $stock = Stock::initial(3);

        self::assertSame(3, $stock->total());
        self::assertSame(3, $stock->available());
        self::assertSame(0, $stock->borrowed());
        self::assertTrue($stock->isFullyAvailable());
        self::assertFalse($stock->isEmpty());
        self::assertFalse($stock->isNotApplicable());
    }

    public function test_it_rejects_non_positive_initial_stock(): void
    {
        $this->expectException(InvalidStock::class);
        $this->expectExceptionMessage(
            'Para recursos físicos, a quantidade deve ser no mínimo 1.'
        );

        Stock::initial(0);
    }

    public function test_it_represents_stock_that_does_not_apply(): void
    {
        $stock = Stock::notApplicable();

        self::assertNull($stock->total());
        self::assertNull($stock->available());
        self::assertSame(0, $stock->borrowed());
        self::assertTrue($stock->isNotApplicable());
        self::assertFalse($stock->isFullyAvailable());
        self::assertFalse($stock->isEmpty());
    }

    public function test_it_restores_valid_persisted_stock(): void
    {
        $stock = Stock::restore(total: 5, available: 3);

        self::assertSame(5, $stock->total());
        self::assertSame(3, $stock->available());
        self::assertSame(2, $stock->borrowed());
    }

    public function test_it_rejects_partially_null_stock(): void
    {
        $this->expectException(InvalidStock::class);
        $this->expectExceptionMessage(
            'Quantidade total e disponível devem ser informadas juntas.'
        );

        Stock::restore(total: 5, available: null);
    }

    public function test_it_rejects_negative_available_stock(): void
    {
        $this->expectException(InvalidStock::class);
        $this->expectExceptionMessage(
            'A quantidade disponível não pode ser negativa.'
        );

        Stock::restore(total: 5, available: -1);
    }

    public function test_it_rejects_available_stock_greater_than_total(): void
    {
        $this->expectException(InvalidStock::class);
        $this->expectExceptionMessage(
            'A quantidade disponível (6) não pode ser maior que a quantidade total (5).'
        );

        Stock::restore(total: 5, available: 6);
    }

    public function test_it_withdraws_one_unit_without_mutating_original_stock(): void
    {
        $original = Stock::initial(3);

        $updated = $original->withdrawOne();

        self::assertSame(3, $original->available());
        self::assertSame(2, $updated->available());
        self::assertSame(1, $updated->borrowed());
    }

    public function test_it_rejects_withdrawal_when_no_unit_is_available(): void
    {
        $stock = Stock::restore(total: 2, available: 0);

        $this->expectException(InsufficientStock::class);
        $this->expectExceptionMessage(
            'Não há unidades disponíveis em estoque.'
        );

        $stock->withdrawOne();
    }

    public function test_it_returns_one_unit_without_mutating_original_stock(): void
    {
        $original = Stock::restore(total: 3, available: 2);

        $updated = $original->returnOne();

        self::assertSame(2, $original->available());
        self::assertSame(3, $updated->available());
        self::assertSame(0, $updated->borrowed());
        self::assertTrue($updated->isFullyAvailable());
    }

    public function test_it_rejects_return_when_stock_is_already_full(): void
    {
        $stock = Stock::initial(3);

        $this->expectException(StockAlreadyFull::class);
        $this->expectExceptionMessage(
            'Não é possível devolver uma unidade: o estoque já está completo.'
        );

        $stock->returnOne();
    }

    public function test_stock_operations_are_no_op_when_stock_does_not_apply(): void
    {
        $stock = Stock::notApplicable();

        self::assertSame($stock, $stock->withdrawOne());
        self::assertSame($stock, $stock->returnOne());
    }

    public function test_it_recalculates_availability_preserving_open_loans(): void
    {
        $stock = Stock::withOpenLoans(total: 5, openLoans: 2);

        self::assertSame(5, $stock->total());
        self::assertSame(3, $stock->available());
        self::assertSame(2, $stock->borrowed());
    }

    public function test_it_rejects_negative_open_loan_count(): void
    {
        $this->expectException(InvalidStock::class);
        $this->expectExceptionMessage(
            'A quantidade de empréstimos abertos não pode ser negativa.'
        );

        Stock::withOpenLoans(total: 5, openLoans: -1);
    }

    public function test_it_rejects_total_below_open_loan_count(): void
    {
        $this->expectException(InvalidStock::class);
        $this->expectExceptionMessage(
            'Impossível reduzir estoque: existem 3 unidades emprestadas.'
        );

        Stock::withOpenLoans(total: 2, openLoans: 3);
    }
}
