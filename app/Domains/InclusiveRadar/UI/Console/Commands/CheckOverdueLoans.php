<?php

namespace App\Domains\InclusiveRadar\UI\Console\Commands;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\InclusiveRadar\Domain\Enums\LoanStatus;
use App\Domains\InclusiveRadar\Domain\Models\Loan;
use App\Domains\InclusiveRadar\Infrastructure\Notifications\LoanOverdueNotification;
use Illuminate\Console\Command;

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

        $this->info($overdueLoans->count().' notificações de atraso enviadas.');
    }
}
