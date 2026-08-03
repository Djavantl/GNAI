<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\Waitlists;

use App\Domains\InclusiveRadar\Application\Queries\Waitlists\WaitlistFormQuery;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class WaitlistFormQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_does_not_list_digital_items_as_waitlistable(): void
    {
        $digitalMaterial = AccessibleEducationalMaterial::factory()
            ->digital()
            ->loanable()
            ->create([
                'name' => 'Slide adaptado',
                'quantity' => null,
                'quantity_available' => null,
                'status' => ResourceStatus::AVAILABLE,
            ]);

        $physicalUnavailableMaterial = AccessibleEducationalMaterial::factory()
            ->physical()
            ->loanable()
            ->create([
                'name' => 'Material físico indisponível',
                'quantity' => 1,
                'quantity_available' => 0,
                'status' => ResourceStatus::AVAILABLE,
            ]);

        $options = app(WaitlistFormQuery::class)->forCreation();

        self::assertFalse($options['educational_materials']->contains(
            static fn (AccessibleEducationalMaterial $item): bool => $item->is($digitalMaterial),
        ));
        self::assertTrue($options['educational_materials']->contains(
            static fn (AccessibleEducationalMaterial $item): bool => $item->is($physicalUnavailableMaterial),
        ));
    }
}
