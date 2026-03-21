<?php

namespace App\Notifications\InclusiveRadar;

use App\Models\InclusiveRadar\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class LoanOverdueNotification extends Notification
{
    use Queueable;

    public function __construct(private Loan $loan) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $beneficiary = $this->loan->student
            ? $this->loan->student->person->name
            : ($this->loan->professional ? $this->loan->professional->person->name : 'N/A');

        $itemName = $this->loan->loanable->name ?? 'Item';
        $daysOverdue = (int) ceil(now()->diffInDays($this->loan->due_date, false));
        $daysOverdue = abs($daysOverdue);

        return [
            'loan_id' => $this->loan->id,
            'title'   => 'Empréstimo Atrasado',
            'message' => "O item '{$itemName}' está com o beneficiário {$beneficiary} e encontra-se atrasado há {$daysOverdue} dia(s).",
            'url'     => route('inclusive-radar.loans.show', $this->loan->id),
            'created_at' => now()->toDateTimeString(),
        ];
    }
}
