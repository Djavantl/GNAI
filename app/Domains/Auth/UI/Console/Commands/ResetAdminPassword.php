<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Console\Commands;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Auth\UI\Console\Concerns\HandlesAdminCredentials;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class ResetAdminPassword extends Command
{
    use HandlesAdminCredentials;

    // A senha é sempre solicitada de forma oculta: não há opção --password
    // para que ela não fique no histórico do shell nem na lista de processos.
    protected $signature = 'auth:reset-admin-password
        {--email= : E-mail do administrador superior}';

    protected $description = 'Redefine a senha de um administrador superior do Gnai.';

    public function handle(): int
    {
        $admins = User::query()
            ->where('is_super_admin', true)
            ->orderBy('email')
            ->get();

        if ($admins->isEmpty()) {
            $this->components->error('Nenhum administrador superior encontrado. Use auth:create-admin para criá-lo.');

            return self::FAILURE;
        }

        $email = (string) ($this->option('email') ?: $this->choice(
            'Selecione o administrador superior',
            $admins->pluck('email')->all(),
            0,
        ));

        $admin = $admins->first(
            fn (User $user): bool => mb_strtolower((string) $user->email) === mb_strtolower($email),
        );

        if (! $admin instanceof User) {
            $this->components->error('O e-mail informado não pertence a um administrador superior.');

            return self::FAILURE;
        }

        $password = $this->askForPassword('Nova senha');

        if (! $this->validatePassword($password)) {
            return self::FAILURE;
        }

        $admin->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
        ])->save();

        // Encerra as sessões abertas com a senha anterior.
        if (config('session.driver') === 'database') {
            DB::table((string) config('session.table', 'sessions'))
                ->where('user_id', $admin->getKey())
                ->delete();
        }

        $this->components->info("Senha do administrador superior {$admin->email} redefinida com sucesso.");

        return self::SUCCESS;
    }
}
