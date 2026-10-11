<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AccessibleEducationalMaterials;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\InclusiveRadar\Domain\Enums\ResourceStatus;
use App\Domains\InclusiveRadar\Domain\Models\AccessibilityFeature;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\UI\Controllers\AccessibleEducationalMaterialController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class ListAccessibleEducationalMaterialsTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/_tests/inclusive-radar/accessible-educational-materials';

    protected function setUp(): void
    {
        parent::setUp();

        Route::get(
            self::ENDPOINT,
            [AccessibleEducationalMaterialController::class, 'index'],
        )->middleware('web');
    }

    public function test_it_filters_physical_inactive_unavailable_materials_by_status(): void
    {
        $user = User::factory()->create();
        $matching = $this->material(
            name: 'Livro Braille inativo',
            isDigital: false,
            isActive: false,
            available: 0,
            status: ResourceStatus::UNAVAILABLE,
        );
        $this->material(
            name: 'Livro Braille ativo',
            isDigital: false,
            isActive: true,
            available: 1,
            status: ResourceStatus::AVAILABLE,
        );
        $this->material(
            name: 'Apostila digital inativa',
            isDigital: true,
            isActive: false,
            available: null,
            status: ResourceStatus::AVAILABLE,
        );

        $response = $this->actingAs($user)->get(
            self::ENDPOINT.'?'.http_build_query([
                'name' => 'Braille',
                'status' => ResourceStatus::UNAVAILABLE->value,
                'is_digital' => '0',
                'is_active' => '0',
                'available' => '0',
            ]),
        );

        $response->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('InclusiveRadar/AccessibleEducationalMaterials/Index')
            ->where('materials.total', 1)
            ->where('materials.data.0.id', $matching->id)
        );
    }

    public function test_it_returns_the_inertia_page_for_an_ajax_request(): void
    {
        $this->material(
            name: 'Livro Braille',
            isDigital: false,
            isActive: true,
            available: 1,
            status: ResourceStatus::AVAILABLE,
        );

        $response = $this->get(
            self::ENDPOINT.'?is_digital=0',
            ['X-Requested-With' => 'XMLHttpRequest'],
        );

        $response->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('InclusiveRadar/AccessibleEducationalMaterials/Index')
            ->has('materials.data', 1)
        );
    }

    public function test_it_filters_materials_by_accessibility_feature(): void
    {
        $user = User::factory()->create();
        $matchingFeature = AccessibilityFeature::factory()
            ->active()
            ->named('Audiodescrição')
            ->create();
        $otherFeature = AccessibilityFeature::factory()
            ->active()
            ->named('Libras')
            ->create();

        $matching = $this->material(
            name: 'Slide adaptado com áudio',
            isDigital: true,
            isActive: true,
            available: null,
            status: ResourceStatus::AVAILABLE,
        );
        $matching->accessibilityFeatures()->sync([$matchingFeature->id]);

        $other = $this->material(
            name: 'Slide adaptado com Libras',
            isDigital: true,
            isActive: true,
            available: null,
            status: ResourceStatus::AVAILABLE,
        );
        $other->accessibilityFeatures()->sync([$otherFeature->id]);

        $response = $this->actingAs($user)->get(
            self::ENDPOINT.'?'.http_build_query([
                'accessibility_feature_id' => $matchingFeature->id,
            ]),
        );

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('InclusiveRadar/AccessibleEducationalMaterials/Index')
            ->where('materials.total', 1)
            ->where('materials.data.0.id', $matching->id)
        );
        $response->assertInertia(fn (Assert $page) => $page
            ->where("accessibilityFeatureOptions.{$matchingFeature->id}", 'Audiodescrição')
            ->where("accessibilityFeatureOptions.{$otherFeature->id}", 'Libras')
        );
    }

    private function material(
        string $name,
        bool $isDigital,
        bool $isActive,
        ?int $available,
        ResourceStatus $status,
    ): AccessibleEducationalMaterial {
        $factory = $isDigital
            ? AccessibleEducationalMaterial::factory()->digital()
            : AccessibleEducationalMaterial::factory()->physical();

        return $factory->loanable()
            ->state([
                'name' => $name,
                'quantity' => $isDigital ? null : 1,
                'quantity_available' => $available,
                'status' => $status,
                'is_active' => $isActive,
            ])
            ->create();
    }
}
