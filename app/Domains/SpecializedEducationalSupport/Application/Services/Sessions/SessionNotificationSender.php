<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Services\Sessions;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use App\Mail\SessionNotification;
use Illuminate\Support\Facades\Mail;

final class SessionNotificationSender
{
    public function send(Session $session, string $subject, string $text): void
    {
        $session->load(['students.person', 'professional.person']);

        $emails = [];

        foreach ($session->students as $student) {
            if ($student->person->email) {
                $emails[] = $student->person->email;
            }
        }

        if ($session->professional?->person?->email) {
            $emails[] = $session->professional->person->email;
        }

        foreach (array_values(array_unique($emails)) as $email) {
            Mail::to($email)->send(
                new SessionNotification($session, $subject, $text),
            );
        }
    }
}
