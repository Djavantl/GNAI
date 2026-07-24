<?php

declare(strict_types=1);

namespace App\Domains\Auth\Tests\Unit\Infrastructure\Notifications;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Auth\Infrastructure\Notifications\ResetPasswordNotification;
use Tests\TestCase;

final class ResetPasswordNotificationTest extends TestCase
{
    public function test_it_builds_custom_password_reset_mail_message(): void
    {
        $user = new User([
            'name' => 'Marley',
            'email' => 'marley@example.com',
        ]);

        $mail = (new ResetPasswordNotification('reset-token'))->toMail($user);

        self::assertSame('Redefinição de senha - GNAI', $mail->subject);
        self::assertSame('emails.auth.reset-password', $mail->view);
        self::assertStringContainsString('/auth/reset-password/reset-token', $mail->viewData['resetUrl']);
        self::assertStringContainsString('email=marley%40example.com', $mail->viewData['resetUrl']);
        self::assertSame('Marley', $mail->viewData['userName']);
    }
}
