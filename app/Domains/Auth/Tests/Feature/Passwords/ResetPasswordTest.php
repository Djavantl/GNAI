<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Feature\Passwords;

use App\Domains\Auth\Domain\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

final class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_reset_password_form_with_token_and_email(): void
    {
        $response = $this->get(route('password.reset', [
            'token' => 'valid-token',
            'email' => 'user@example.com',
        ]));

        $response
            ->assertOk()
            ->assertViewIs('auth.reset-password')
            ->assertViewHas('token', 'valid-token')
            ->assertViewHas('email', 'user@example.com');
    }

    public function test_it_resets_user_password_with_valid_token(): void
    {
        Event::fake([PasswordReset::class]);

        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123'),
            'remember_token' => 'old-remember-token',
        ]);
        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Senha alterada!');

        $user->refresh();

        self::assertTrue(Hash::check('NewPassword123', $user->password));
        self::assertNotSame('old-remember-token', $user->remember_token);
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => $user->email,
        ]);
        Event::assertDispatched(PasswordReset::class);
    }

    public function test_it_rejects_invalid_token_without_changing_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword123'),
        ]);

        $response = $this->from(route('password.reset', [
            'token' => 'invalid-token',
            'email' => $user->email,
        ]))->post(route('password.update'), [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response
            ->assertRedirect(route('password.reset', [
                'token' => 'invalid-token',
                'email' => $user->email,
            ]))
            ->assertSessionHasErrors('email');

        self::assertTrue(Hash::check('OldPassword123', $user->refresh()->password));
    }
}
