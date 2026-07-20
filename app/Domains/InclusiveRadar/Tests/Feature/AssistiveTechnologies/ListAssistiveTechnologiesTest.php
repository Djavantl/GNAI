<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Tests\Feature\AssistiveTechnologies;

use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\UI\Controllers\AssistiveTechnologyController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
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
            digital: false,
            active: false,
            available: 0,
        );
        $this->technology(
            name: 'Linha Braille ativa',
            digital: false,
            active: true,
            available: 1,
        );
        $this->technology(
            name: 'Leitor digital inativo',
            digital: true,
            active: false,
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
        $response->assertViewIs(
            'pages.inclusive-radar.assistive-technologies.index',
        );
        $response->assertViewHas(
            'assistiveTechnologies',
            static fn ($technologies): bool => $technologies->total() === 1
                && $technologies->first()->is($matching),
        );
    }

    public function test_it_returns_only_the_table_for_an_ajax_request(): void
    {
        $this->technology(
            name: 'Linha Braille',
            digital: false,
            active: true,
            available: 1,
        );

        $response = $this->get(
            self::ENDPOINT.'?is_digital=0',
            ['X-Requested-With' => 'XMLHttpRequest'],
        );

        $response->assertOk();
        $response->assertViewIs(
            'pages.inclusive-radar.assistive-technologies.partials.table',
        );
        $response->assertViewHas('assistiveTechnologies');
    }

    private function technology(
        string $name,
        bool $digital,
        bool $active,
        ?int $available,
    ): AssistiveTechnology {
        $factory = $digital
            ? AssistiveTechnology::factory()->digital()
            : AssistiveTechnology::factory()->physical();

        return $factory->loanable()
            ->state([
                'name' => $name,
                'quantity' => $digital ? null : 1,
                'quantity_available' => $available,
                'is_active' => $active,
            ])
            ->create();
    }
}
