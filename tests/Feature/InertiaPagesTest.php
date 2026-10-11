<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domains\Auth\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class InertiaPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_initial_visits_boot_react_without_the_previous_layout(): void
    {
        $this->withoutVite();

        $response = $this->get('/auth/login');

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Auth/Login')->where('auth.user', null))
            ->assertSee('data-page="root"', false)
            ->assertDontSee('bootstrap.min.css', false)
            ->assertDontSee('resources/js/app.js', false);

        $this->get('/about-us')->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('AboutUs/AboutUs'));
    }

    public function test_authenticated_screens_use_inertia_and_hide_user_secrets(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['is_admin' => true]);
        $this->actingAs($user);

        foreach ([
            '/inclusive-radar/institutions' => 'InclusiveRadar/Institutions/Index',
            '/inclusive-radar/barriers' => 'InclusiveRadar/Barriers/Index',
            '/specialized-educational-support/students' => 'SpecializedEducationalSupport/Students/Index',
            '/specialized-educational-support/sessions' => 'SpecializedEducationalSupport/Sessions/Index',
            '/notifications' => 'Notifications/Index',
        ] as $url => $component) {
            $this->get($url)->assertOk()->assertInertia(fn (Assert $page) => $page
                ->component($component)
                ->where('auth.user.id', $user->id)
                ->missing('auth.user.password')
                ->missing('auth.user.remember_token')
            );
        }
    }

    public function test_inertia_partial_reload_returns_filtered_props_instead_of_html(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['is_admin' => true]));
        $url = '/inclusive-radar/barriers';
        $initial = $this->get($url)->assertOk();

        $this->get($url.'?search=teste', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => $initial->viewData('page')['version'],
            'X-Inertia-Partial-Component' => 'InclusiveRadar/Barriers/Index',
            'X-Inertia-Partial-Data' => 'barriers',
            'X-Requested-With' => 'XMLHttpRequest',
            'Accept' => 'text/html, application/xhtml+xml',
        ])->assertOk()
            ->assertHeader('X-Inertia', 'true')
            ->assertJsonPath('component', 'InclusiveRadar/Barriers/Index')
            ->assertJsonStructure(['props' => ['barriers' => ['data', 'links']]])
            ->assertJsonMissingPath('props.auth');
    }

    public function test_shared_flash_and_validation_errors_reach_react(): void
    {
        $this->withoutVite();
        $this->from('/auth/login')->post('/auth/login', [])->assertRedirect('/auth/login');
        $this->get('/auth/login')->assertInertia(fn (Assert $page) => $page
            ->has('errors.email')
            ->has('errors.password')
        );

        $this->withSession(['success' => '</script><script>alert(1)</script>'])
            ->get('/about-us')
            ->assertInertia(fn (Assert $page) => $page
                ->where('flash.success', '</script><script>alert(1)</script>')
            )
            ->assertDontSee('</script><script>alert(1)</script>', false);
    }

    public function test_missing_pages_render_a_react_error_and_api_requests_stay_json(): void
    {
        $this->withoutVite();
        $this->get('/nonexistent-screen')->assertNotFound()
            ->assertInertia(fn (Assert $page) => $page->component('Errors/Error')->where('status', 404));

        $this->getJson('/nonexistent-screen')->assertNotFound()
            ->assertJsonStructure(['message']);
    }
}
