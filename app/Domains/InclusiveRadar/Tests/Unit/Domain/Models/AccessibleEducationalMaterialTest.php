<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Unit\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\AccessibleEducationalMaterials\CreateAccessibleEducationalMaterialDTO;
use App\Domains\InclusiveRadar\Domain\DTOs\AccessibleEducationalMaterials\UpdateAccessibleEducationalMaterialDTO;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidAccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Exceptions\InvalidStock;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\ValueObjects\AssetCode;
use PHPUnit\Framework\TestCase;

final class AccessibleEducationalMaterialTest extends TestCase
{
    public function test_it_registers_a_physical_material_with_initial_stock(): void
    {
        $material = $this->register(
            name: 'Livro em Braille',
            digital: false,
            loanable: true,
            quantity: 3,
            assetCode: AssetCode::from('MPA-1001'),
            conservationState: ConservationState::GOOD,
        );

        self::assertFalse($material->exists);
        self::assertSame('Livro em Braille', $material->name);
        self::assertFalse($material->is_digital);
        self::assertTrue($material->is_loanable);
        self::assertSame(3, $material->quantity);
        self::assertSame(3, $material->quantity_available);
        self::assertSame('MPA-1001', $material->asset_code);
        self::assertSame(ConservationState::GOOD, $material->conservation_state);
        self::assertSame(ResourceStatus::AVAILABLE, $material->status);
    }

    public function test_it_registers_a_digital_material_without_stock(): void
    {
        $material = $this->register(
            name: 'Apostila digital acessível',
            digital: true,
            loanable: true,
            quantity: null,
            assetCode: null,
            conservationState: ConservationState::NOT_APPLICABLE,
        );

        self::assertTrue($material->is_loanable);
        self::assertNull($material->quantity);
        self::assertNull($material->quantity_available);
    }

    public function test_it_rejects_empty_name(): void
    {
        $this->expectException(InvalidAccessibleEducationalMaterial::class);

        $this->register(
            name: '   ',
            digital: true,
            loanable: false,
            quantity: null,
            assetCode: null,
            conservationState: ConservationState::NOT_APPLICABLE,
        );
    }

    public function test_it_rejects_assigning_an_empty_target_audience(): void
    {
        $this->expectException(InvalidAccessibleEducationalMaterial::class);

        $material = $this->register(
            name: 'Apostila digital acessível',
            digital: true,
            loanable: false,
            quantity: null,
            assetCode: null,
            conservationState: ConservationState::NOT_APPLICABLE,
        );

        $material->assignTargetAudience([]);
    }

    public function test_it_rejects_physical_material_without_quantity(): void
    {
        $this->expectException(InvalidStock::class);

        $this->register(
            name: 'Mapa tátil',
            digital: false,
            loanable: true,
            quantity: null,
            assetCode: null,
            conservationState: ConservationState::GOOD,
        );
    }

    public function test_it_revises_physical_stock_preserving_open_loans(): void
    {
        $material = $this->register(
            name: 'Livro em Braille',
            digital: false,
            loanable: true,
            quantity: 3,
            assetCode: AssetCode::from('MPA-1001'),
            conservationState: ConservationState::GOOD,
        );

        $this->revise($material,
            name: 'Livro em Braille atualizado',
            digital: false,
            loanable: true,
            quantity: 5,
            assetCode: AssetCode::from('MPA-1001'),
            conservationState: ConservationState::REGULAR,
            status: ResourceStatus::AVAILABLE,
            openLoans: 2,
        );

        self::assertSame('Livro em Braille atualizado', $material->name);
        self::assertSame(5, $material->quantity);
        self::assertSame(3, $material->quantity_available);
        self::assertSame(ConservationState::REGULAR, $material->conservation_state);
    }

    public function test_it_rejects_reducing_stock_below_open_loans(): void
    {
        $this->expectException(InvalidStock::class);

        $material = $this->register(
            name: 'Mapa tátil',
            digital: false,
            loanable: true,
            quantity: 3,
            assetCode: null,
            conservationState: ConservationState::GOOD,
        );

        $this->revise($material,
            name: 'Mapa tátil',
            digital: false,
            loanable: true,
            quantity: 1,
            assetCode: null,
            conservationState: ConservationState::GOOD,
            status: ResourceStatus::AVAILABLE,
            openLoans: 2,
        );
    }

    public function test_it_rejects_status_change_while_there_are_open_loans(): void
    {
        $this->expectException(InvalidAccessibleEducationalMaterial::class);

        $material = $this->register(
            name: 'Mapa tátil',
            digital: false,
            loanable: true,
            quantity: 2,
            assetCode: null,
            conservationState: ConservationState::GOOD,
        );

        $this->revise($material,
            name: 'Mapa tátil',
            digital: false,
            loanable: true,
            quantity: 2,
            assetCode: null,
            conservationState: ConservationState::GOOD,
            status: ResourceStatus::UNDER_MAINTENANCE,
            openLoans: 1,
        );
    }

    private function register(
        string $name,
        bool $digital,
        bool $loanable,
        ?int $quantity,
        ?AssetCode $assetCode,
        ConservationState $conservationState,
        ResourceStatus $status = ResourceStatus::AVAILABLE,
        ?string $notes = null,
        bool $active = true,
    ): AccessibleEducationalMaterial {
        return AccessibleEducationalMaterial::register(new CreateAccessibleEducationalMaterialDTO(
            name: $name,
            digital: $digital,
            loanable: $loanable,
            quantity: $quantity,
            assetCode: $assetCode,
            conservationState: $conservationState,
            status: $status,
            notes: $notes,
            active: $active,
        ));
    }

    private function revise(
        AccessibleEducationalMaterial $material,
        string $name,
        bool $digital,
        bool $loanable,
        ?int $quantity,
        ?AssetCode $assetCode,
        ConservationState $conservationState,
        ResourceStatus $status,
        int $openLoans,
        ?string $notes = null,
        bool $active = true,
    ): void {
        $material->revise(new UpdateAccessibleEducationalMaterialDTO(
            name: $name,
            digital: $digital,
            loanable: $loanable,
            quantity: $quantity,
            assetCode: $assetCode,
            conservationState: $conservationState,
            status: $status,
            openLoans: $openLoans,
            notes: $notes,
            active: $active,
        ));
    }
}
