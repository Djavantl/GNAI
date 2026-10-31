<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\UI\Presenters;

use App\Domains\Inclusion\Domain\Models\Waitlist;
use App\Shared\Infrastructure\Security\RichTextSanitizer;

final class WaitlistPresenter
{
    /**
     * @return array<string, mixed>
     */
    public function waitlist(Waitlist $waitlist, bool $includeObservationHtml = false): array
    {
        $waitlist->loadMissing([
            'waitlistable',
            'student:id,person_id,registration',
            'student.person:id,name',
            'professional:id,person_id',
            'professional.person:id,name',
            'user:id,name',
        ]);

        $payload = [
            'id' => $waitlist->id,
            'student_id' => $waitlist->student_id,
            'professional_id' => $waitlist->professional_id,
            'waitlistable_id' => $waitlist->waitlistable_id,
            'item_type' => $waitlist->waitlistable?->item_type?->value,
            'item_name' => $waitlist->waitlistable?->name ?? 'Item removido',
            'asset_code' => $waitlist->waitlistable?->asset_code,
            'student' => $waitlist->student ? [
                'name' => $waitlist->student->person?->name ?? 'Sem nome',
                'registration' => $waitlist->student->registration,
            ] : null,
            'professional' => $waitlist->professional ? [
                'name' => $waitlist->professional->person?->name ?? 'Sem nome',
            ] : null,
            'user' => $waitlist->user?->name ?? 'Não informado',
            'requested_at' => $waitlist->requested_at?->format('Y-m-d\TH:i:s'),
            'requested_at_label' => $waitlist->requested_at?->format('d/m/Y H:i') ?? 'Não informado',
            'status' => $waitlist->status->value,
            'status_label' => $waitlist->status->label(),
            'status_color' => $waitlist->status->color(),
            'observation' => $waitlist->observation,
        ];

        if ($includeObservationHtml) {
            $payload['observation_html'] = RichTextSanitizer::sanitize($waitlist->observation ?? '');
        }

        return $payload;
    }
}
