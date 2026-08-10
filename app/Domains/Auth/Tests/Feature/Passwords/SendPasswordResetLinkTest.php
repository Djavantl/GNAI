<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Feature\Passwords;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Auth\Infrastructure\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class SendPasswordResetLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_password_reset_link_to_registered_user(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        $response
            ->assertRedirect()
            ->assertSessionHas('status', 'Se o e-mail estiver cadastrado, enviaremos instruções.');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);
    }
}
