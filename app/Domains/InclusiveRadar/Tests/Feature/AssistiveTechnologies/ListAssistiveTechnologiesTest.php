<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AssistiveTechnologies;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\UI\Controllers\AssistiveTechnologyController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class ListAssistiveTechnologiesTest extends TestCase
{
    use RefreshDatabase;

    private const ENDPOINT = '/_tests/inclusive-radar/assistive-technologies';

    protected function setUp(): void
    {
        parent::setUp();

        Route::get(
            self::ENDPOINT,
            [AssistiveTechnologyController::class, 'index'],
        )->middleware('web');
    }

    public function test_it_filters_physical_inactive_and_unavailable_technologies(): void
    {
        $user = User::factory()->create();
        $matching = $this->technology(
            name: 'Linha Braille inativa',
            isDigital: false,
            isActive: false,
            available: 0,
        );
        $this->technology(
            name: 'Linha Braille ativa',
            isDigital: false,
            isActive: true,
            available: 1,
        );
        $this->technology(
            name: 'Leitor digital inativo',
            isDigital: true,
            isActive: false,
            available: null,
        );

        $response = $this->actingAs($user)->get(
            self::ENDPOINT.'?'.http_build_query([
                'name' => 'Braille',
                'is_digital' => '0',
                'is_active' => '0',
                'available' => '0',
            ]),
        );

        $response->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('InclusiveRadar/AssistiveTechnologies/Index')
            ->where('assistiveTechnologies.total', 1)
            ->where('assistiveTechnologies.data.0.id', $matching->id)
        );
    }

    public function test_it_returns_the_inertia_page_for_an_ajax_request(): void
    {
        $this->technology(
            name: 'Linha Braille',
            isDigital: false,
            isActive: true,
            available: 1,
        );

        $response = $this->get(
            self::ENDPOINT.'?is_digital=0',
            ['X-Requested-With' => 'XMLHttpRequest'],
        );

        $response->assertOk();

        $response->assertInertia(fn (Assert $page) => $page
            ->component('InclusiveRadar/AssistiveTechnologies/Index')
            ->has('assistiveTechnologies.data', 1)
        );
    }

    private function technology(
        string $name,
        bool $isDigital,
        bool $isActive,
        ?int $available,
    ): AssistiveTechnology {
        $factory = $isDigital
            ? AssistiveTechnology::factory()->digital()
            : AssistiveTechnology::factory()->physical();

        return $factory->loanable()
            ->state([
                'name' => $name,
                'quantity' => $isDigital ? null : 1,
                'quantity_available' => $available,
                'is_active' => $isActive,
            ])
            ->create();
    }
}
