<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domains\Auth\Application\Permissions\PermissionRegistry;
use App\Domains\Auth\Domain\Models\Permission;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\Models\Institution;
use App\Domains\Inclusion\Domain\Models\Professional;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

final class AppNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_hides_private_navigation_from_guests(): void
    {
        $this->withoutVite();

        $this->get('/auth/login')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('navigation.entries', [])
            ->where('topbar', null)
            ->where('impersonating', false)
        );
    }

    public function test_it_gives_admins_every_section_with_relative_links_and_the_active_nested_item(): void
    {
        $this->withoutVite();
        $this->actingAs(User::factory()->create(['is_admin' => true]));

        $response = $this->get('/inclusive-radar/institutions');

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('navigation.currentItemKey', 'institutions')
            ->where('topbar.institutionName', 'Gnai')
            ->where('navigation.entries', function ($entries): bool {
                $entries = collect($entries);
                $registrations = $entries->firstWhere('key', 'registrations');
                $links = $entries->filter(fn ($entry) => isset($entry['href']))
                    ->merge($registrations['children'] ?? []);

                self::assertSame(['Outros'], $entries->pluck('title')->filter()->values()->all());
                self::assertContains('institutions', array_column($registrations['children'] ?? [], 'key'));
                self::assertSame('/auth/dashboard', $links->firstWhere('key', 'dashboard')['href']);
                foreach (['reports', 'backups', 'audit', 'settings', 'website'] as $adminOnly) {
                    self::assertNotNull($links->firstWhere('key', $adminOnly), $adminOnly);
                }
                foreach ($links as $link) {
                    self::assertStringStartsWith('/', $link['href']);
                    self::assertStringNotContainsString('.html', $link['href']);
                }

                return true;
            })
        );
    }

    public function test_it_limits_restricted_professionals_to_allowed_items_and_own_record_routes(): void
    {
        $this->withoutVite();
        // Isolate this test from the developer machine's generated permission manifest.
        $registry = app(PermissionRegistry::class);
        (new \ReflectionProperty($registry, 'manifestPermissions'))->setValue($registry, $registry->discover());
        $professional = Professional::factory()->active()->create();
        $slugs = ['student.view', 'session.view-own', 'aee-record.view-own', 'pedagogical-record.view-own'];
        $ids = collect($slugs)->map(fn ($slug) => Permission::query()->firstOrCreate(['slug' => $slug], ['name' => $slug])->id);
        $professional->position->permissions()->sync($ids);
        $user = User::factory()->create(['is_admin' => false, 'professional_id' => $professional->id]);

        $this->actingAs($user)->get('/notifications')->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('navigation.entries', function ($entries): bool {
                $items = collect($entries)->filter(fn ($entry) => isset($entry['href']))->keyBy('key');
                self::assertSame(['dashboard', 'notifications', 'students', 'sessions', 'aee-records', 'pedagogical-records', 'about'], $items->keys()->all());
                self::assertNull(collect($entries)->firstWhere('key', 'registrations'));
                foreach (['sessions' => 'my-sessions', 'aee-records' => 'my-records', 'pedagogical-records' => 'my-records'] as $key => $action) {
                    self::assertSame(route("specialized-educational-support.$key.$action", absolute: false), $items[$key]['href']);
                }

                return true;
            })
        );
    }

    public function test_it_shows_only_the_current_users_unread_notifications_and_impersonation_state(): void
    {
        $this->withoutVite();
        $user = User::factory()->create(['is_admin' => true]);
        $other = User::factory()->create(['is_admin' => true]);
        Institution::factory()->create(['name' => 'Instituição do Gnai']);
        foreach ([[$user, 'Minha notificação', null], [$user, 'Já lida', now()], [$other, 'Privada de outro usuário', null]] as [$owner, $title, $readAt]) {
            $owner->notifications()->create([
                'id' => (string) Str::uuid(),
                'type' => 'test',
                'data' => ['title' => $title, 'message' => 'Mensagem', 'url' => '/notifications'],
                'read_at' => $readAt,
            ]);
        }

        $this->actingAs($user)->withSession(['impersonator_id' => $other->id])->get('/notifications')
            ->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('impersonating', true)
            ->where('topbar.unreadCount', 1)
            ->where('topbar.institutionName', 'Instituição do Gnai')
            ->has('topbar.notifications', 1)
            ->where('topbar.notifications.0.title', 'Minha notificação')
            ->where('topbar.notifications.0.href', '/notifications')
            ->has('auth.user.photo_url')
            ->missing('auth.user.password')
            );
    }
}
