<?php

declare(strict_types=1);

namespace App\Domains\Settings\Infrastructure\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

final class SettingsTestMail extends Mailable
{
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Teste de configuração de e-mail, Gnai',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.settings.test-mail',
            text: 'emails.settings.test-mail-text',
        );
    }
}
