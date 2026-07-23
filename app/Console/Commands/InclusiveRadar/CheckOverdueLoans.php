<?php

namespace App\Console\Commands\InclusiveRadar;

use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use Illuminate\Console\Command;
use App\Notifications\InclusiveRadar\LoanOverdueNotification;
use App\Models\User;

class CheckOverdueLoans extends Command
{
    protected $signature = 'loans:check-overdue';
    protected $description = 'Verifica empréstimos atrasados e notifica os administradores';

    public function handle()
    {
        // Pega todos os empréstimos ativos cuja data de entrega já passou
        $overdueLoans = Loan::where('status', LoanStatus::ACTIVE->value)
            ->where('due_date', '<=', today())
            ->get();

        if ($overdueLoans->isEmpty()) {
            $this->info('Nenhum empréstimo atrasado hoje.');
            return;
        }

        $admins = User::all();

        foreach ($overdueLoans as $loan) {
            foreach ($admins as $admin) {
                $admin->notify(new LoanOverdueNotification($loan));
            }
        }

        $this->info($overdueLoans->count() . ' notificações de atraso enviadas.');
    }
}
