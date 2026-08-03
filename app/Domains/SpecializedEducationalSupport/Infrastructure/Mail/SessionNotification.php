<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Infrastructure\Mail;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class SessionNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Session $session,
        public string $title,
        public string $messageContent,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.specialized-educational-support.session-notification',
        );
    }
}
