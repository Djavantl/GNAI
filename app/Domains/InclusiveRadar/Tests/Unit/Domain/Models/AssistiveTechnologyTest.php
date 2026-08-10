<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\AssistiveTechnologies\CreateAssistiveTechnologyDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\AssistiveTechnologies\UpdateAssistiveTechnologyDTO;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidAssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidStock;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;
use PHPUnit\Framework\TestCase;

final class AssistiveTechnologyTest extends TestCase
{
    public function test_it_registers_a_physical_technology_with_initial_stock(): void
    {
        $technology = $this->register(
            name: 'Linha Braille',
            isDigital: false,
            isLoanable: true,
            quantity: 3,
            assetCode: AssetCode::from('TA-1001'),
            conservationState: ConservationState::GOOD,
        );

        self::assertFalse($technology->exists);
        self::assertSame('Linha Braille', $technology->name);
        self::assertFalse($technology->is_digital);
        self::assertTrue($technology->is_loanable);
        self::assertSame(3, $technology->quantity);
        self::assertSame(3, $technology->quantity_available);
        self::assertSame('TA-1001', $technology->asset_code);
        self::assertSame(ConservationState::GOOD, $technology->conservation_state);
        self::assertSame(ResourceStatus::AVAILABLE, $technology->status);
    }

    public function test_it_registers_a_digital_loanable_technology_without_stock(): void
    {
        $technology = $this->register(
            name: 'Leitor de tela',
            isDigital: true,
            isLoanable: true,
            quantity: null,
            assetCode: null,
            conservationState: ConservationState::NOT_APPLICABLE,
        );

        self::assertTrue($technology->is_loanable);
        self::assertNull($technology->quantity);
        self::assertNull($technology->quantity_available);
        self::assertTrue($technology->stock()->isNotApplicable());
    }

    public function test_it_trims_name(): void
    {
        $technology = $this->register(
            name: ' Linha Braille ',
            isDigital: true,
            isLoanable: false,
            quantity: null,
            assetCode: null,
            conservationState: ConservationState::NOT_APPLICABLE,
        );

        self::assertSame('Linha Braille', $technology->name);
    }

    public function test_it_rejects_assigning_an_empty_target_audience(): void
    {
        $this->expectException(InvalidAssistiveTechnology::class);

        $technology = $this->register(
            name: 'Leitor de tela',
            isDigital: true,
            isLoanable: false,
            quantity: null,
            assetCode: null,
            conservationState: ConservationState::NOT_APPLICABLE,
        );

        $technology->assignTargetAudience([]);
    }

    public function test_it_rejects_physical_technology_without_quantity(): void
    {
        $this->expectException(InvalidStock::class);

        $this->register(
            name: 'Mouse adaptado',
            isDigital: false,
            isLoanable: true,
            quantity: null,
            assetCode: null,
            conservationState: ConservationState::GOOD,
        );
    }

    public function test_it_revises_physical_stock_preserving_open_loans(): void
    {
        $technology = $this->register(
            name: 'Linha Braille',
            isDigital: false,
            isLoanable: true,
            quantity: 3,
            assetCode: AssetCode::from('TA-1001'),
            conservationState: ConservationState::GOOD,
        );

        $this->revise($technology,
            name: 'Linha Braille atualizada',
            isDigital: false,
            isLoanable: true,
            quantity: 5,
            assetCode: AssetCode::from('TA-1001'),
            conservationState: ConservationState::REGULAR,
            status: ResourceStatus::AVAILABLE,
            openLoans: 2,
        );

        self::assertSame('Linha Braille atualizada', $technology->name);
        self::assertSame(5, $technology->quantity);
        self::assertSame(3, $technology->quantity_available);
        self::assertSame(ConservationState::REGULAR, $technology->conservation_state);
    }

    public function test_it_revises_a_digital_technology_without_manipulating_stock(): void
    {
        $technology = $this->register(
            name: 'Leitor de tela',
            isDigital: false,
            isLoanable: true,
            quantity: 2,
            assetCode: null,
            conservationState: ConservationState::GOOD,
        );

        $this->revise($technology,
            name: 'Leitor de tela',
            isDigital: true,
            isLoanable: true,
            quantity: null,
            assetCode: null,
            conservationState: ConservationState::NOT_APPLICABLE,
            status: ResourceStatus::AVAILABLE,
            openLoans: 1,
        );

        self::assertTrue($technology->is_loanable);
        self::assertNull($technology->quantity);
        self::assertNull($technology->quantity_available);
    }

    public function test_it_rejects_reducing_stock_below_open_loans(): void
    {
        $this->expectException(InvalidStock::class);

        $technology = $this->register(
            name: 'Mouse adaptado',
            isDigital: false,
            isLoanable: true,
            quantity: 3,
            assetCode: null,
            conservationState: ConservationState::GOOD,
        );

        $this->revise($technology,
            name: 'Mouse adaptado',
            isDigital: false,
            isLoanable: true,
            quantity: 1,
            assetCode: null,
            conservationState: ConservationState::GOOD,
            status: ResourceStatus::AVAILABLE,
            openLoans: 2,
        );
    }

    public function test_it_rejects_status_change_while_there_are_open_loans(): void
    {
        $this->expectException(InvalidAssistiveTechnology::class);

        $technology = $this->register(
            name: 'Mouse adaptado',
            isDigital: false,
            isLoanable: true,
            quantity: 2,
            assetCode: null,
            conservationState: ConservationState::GOOD,
        );

        $this->revise($technology,
            name: 'Mouse adaptado',
            isDigital: false,
            isLoanable: true,
            quantity: 2,
            assetCode: null,
            conservationState: ConservationState::GOOD,
            status: ResourceStatus::UNDER_MAINTENANCE,
            openLoans: 1,
        );
    }

    private function register(
        string $name,
        bool $isDigital,
        bool $isLoanable,
        ?int $quantity,
        ?AssetCode $assetCode,
        ConservationState $conservationState,
        ResourceStatus $status = ResourceStatus::AVAILABLE,
        ?string $notes = null,
        bool $active = true,
    ): AssistiveTechnology {
        return AssistiveTechnology::register(new CreateAssistiveTechnologyDTO(
            name: $name,
            isDigital: $isDigital,
            isLoanable: $isLoanable,
            quantity: $quantity,
            assetCode: $assetCode,
            conservationState: $conservationState,
            status: $status,
            notes: $notes,
            isActive: $active,
        ));
    }

    private function revise(
        AssistiveTechnology $technology,
        string $name,
        bool $isDigital,
        bool $isLoanable,
        ?int $quantity,
        ?AssetCode $assetCode,
        ConservationState $conservationState,
        ResourceStatus $status,
        int $openLoans,
        ?string $notes = null,
        bool $active = true,
    ): void {
        $technology->revise(new UpdateAssistiveTechnologyDTO(
            name: $name,
            isDigital: $isDigital,
            isLoanable: $isLoanable,
            quantity: $quantity,
            assetCode: $assetCode,
            conservationState: $conservationState,
            status: $status,
            openLoans: $openLoans,
            notes: $notes,
            isActive: $active,
        ));
    }
}
