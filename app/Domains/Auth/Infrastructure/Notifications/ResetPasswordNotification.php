<?php

declare(strict_types=1);

namespace App\Domains\Auth\Infrastructure\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $token,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject('Redefinição de senha - GNAI')
            ->view('emails.auth.reset-password', [
                'resetUrl' => $resetUrl,
                'expiresInMinutes' => (int) config('auth.passwords.users.expire', 60),
                'userName' => $notifiable->name ?: null,
                'appName' => config('app.name', 'GNAI'),
            ]);
    }
}
