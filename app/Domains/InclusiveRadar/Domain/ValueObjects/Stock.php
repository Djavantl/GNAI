<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\ValueObjects;

use App\Domains\InclusiveRadar\Domain\Exceptions\InsufficientStock;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidStock;
use App\Domains\InclusiveRadar\Domain\Exceptions\StockAlreadyFull;

final readonly class Stock
{
    private function __construct(
        private ?int $total,
        private ?int $available,
    ) {
        $this->ensureValidState();
    }

    /**
     * Cria o estoque inicial de um recurso físico.
     *
     * No cadastro, todas as unidades começam disponíveis.
     */
    public static function initial(int $total): self
    {
        if ($total <= 0) {
            throw new InvalidStock(
                'Para recursos físicos, a quantidade deve ser no mínimo 1.'
            );
        }

        return new self(
            total: $total,
            available: $total,
        );
    }

    /**
     * Recursos digitais não possuem estoque físico controlado.
     */
    public static function notApplicable(): self
    {
        return new self(
            total: null,
            available: null,
        );
    }

    /**
     * Restaura um estoque previamente persistido.
     *
     * Deve ser usado pelo Repository/Mapper ao reconstruir a Entity.
     */
    public static function restore(?int $total, ?int $available): self
    {
        return new self($total, $available);
    }

    /**
     * Recalcula o estoque após alterar a quantidade total.
     *
     * Preserva o número de unidades atualmente emprestadas:
     *
     * disponíveis = total - empréstimos abertos
     */
    public static function withOpenLoans(int $total, int $openLoans): self
    {
        if ($total <= 0) {
            throw new InvalidStock(
                'Para recursos físicos, a quantidade deve ser no mínimo 1.'
            );
        }

        if ($openLoans < 0) {
            throw new InvalidStock(
                'A quantidade de empréstimos abertos não pode ser negativa.'
            );
        }

        if ($total < $openLoans) {
            throw new InvalidStock(
                "Impossível reduzir estoque: existem {$openLoans} unidades emprestadas."
            );
        }

        return new self(
            total: $total,
            available: $total - $openLoans,
        );
    }

    public function withdrawOne(): self
    {
        if ($this->isNotApplicable()) {
            return $this;
        }

        if ($this->available === 0) {
            throw new InsufficientStock;
        }

        return new self(
            total: $this->total,
            available: $this->available - 1,
        );
    }

    public function returnOne(): self
    {
        if ($this->isNotApplicable()) {
            return $this;
        }

        if ($this->available === $this->total) {
            throw new StockAlreadyFull;
        }

        return new self(
            total: $this->total,
            available: $this->available + 1,
        );
    }

    public function total(): ?int
    {
        return $this->total;
    }

    public function available(): ?int
    {
        return $this->available;
    }

    public function borrowed(): int
    {
        if ($this->isNotApplicable()) {
            return 0;
        }

        return $this->total - $this->available;
    }

    public function isEmpty(): bool
    {
        return $this->available === 0;
    }

    public function isFullyAvailable(): bool
    {
        return ! $this->isNotApplicable()
            && $this->available === $this->total;
    }

    public function isNotApplicable(): bool
    {
        return $this->total === null;
    }

    private function ensureValidState(): void
    {
        if ($this->total === null && $this->available === null) {
            return;
        }

        if ($this->total === null || $this->available === null) {
            throw new InvalidStock(
                'Quantidade total e disponível devem ser informadas juntas.'
            );
        }

        if ($this->total <= 0) {
            throw new InvalidStock(
                'Para recursos físicos, a quantidade deve ser no mínimo 1.'
            );
        }

        if ($this->available < 0) {
            throw new InvalidStock(
                'A quantidade disponível não pode ser negativa.'
            );
        }

        if ($this->available > $this->total) {
            throw new InvalidStock(
                "A quantidade disponível ({$this->available}) não pode ser "
                ."maior que a quantidade total ({$this->total})."
            );
        }
    }
}
