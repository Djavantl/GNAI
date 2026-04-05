<?php

namespace App\Notifications\InclusiveRadar;

use App\Models\InclusiveRadar\Waitlist;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ItemAvailableNotification extends Notification
{
    use Queueable;

    public function __construct(private Waitlist $waitlist) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $itemName = $this->waitlist->waitlistable->name ?? 'Recurso';

        $beneficiaryName = $this->waitlist->student
            ? $this->waitlist->student->person->name
            : ($this->waitlist->professional ? $this->waitlist->professional->person->name : 'Beneficiário desconhecido');

        return [
            'waitlist_id' => $this->waitlist->id,
            'title'       => 'Próximo da fila disponível',
            'message'     => "O item '{$itemName}' está disponível para o beneficiário: {$beneficiaryName}. Realize o empréstimo.",
            'url' => route('inclusive-radar.loans.create', [
                'item_id'         => $this->waitlist->waitlistable_id,
                'item_type'       => $this->waitlist->waitlistable_type,
                'student_id'      => $this->waitlist->student_id,
                'professional_id' => $this->waitlist->professional_id,
            ]),
            'created_at'  => now()->toDateTimeString(),
        ];
    }
}
