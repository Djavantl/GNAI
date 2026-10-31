<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Console\Commands;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Auth\UI\Console\Concerns\HandlesAdminCredentials;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

final class CreateAdminUser extends Command
{
    use HandlesAdminCredentials;

    protected $signature = 'auth:create-admin
        {--name= : Nome do administrador superior. Em promoção, atualiza o nome apenas se informado}
        {--email= : E-mail do administrador superior}
        {--password= : Senha do administrador. Evite em terminal compartilhado; pode aparecer no histórico/processos}
        {--promote : Promove um usuário existente, sem vínculo com a equipe, com o e-mail informado}';

    protected $description = 'Cria o administrador superior do Gnai ou promove um usuário existente sem vínculo com a equipe.';

    public function handle(): int
    {
        $email = (string) ($this->option('email') ?: $this->ask('E-mail'));

        if (! $this->validateEmail($email)) {
            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->first();

        if ($user instanceof User) {
            if ($user->isSuperAdmin()) {
                $this->components->info('Este usuário já é administrador superior.');

                return self::SUCCESS;
            }

            if ($user->hasPersonProfile()) {
                $this->components->error('Este e-mail pertence a um membro da equipe. O administrador superior deve ser uma conta própria; administradores internos são definidos no cadastro da equipe pela conta superior.');

                return self::FAILURE;
            }

            if (! $this->option('promote') && ! $this->confirm('Usuário já existe. Promover para administrador superior?')) {
                $this->components->warn('Operação cancelada.');

                return self::FAILURE;
            }

            $attributes = [
                'is_admin' => true,
                'is_super_admin' => true,
                'role' => 'admin',
            ];

            $name = $this->option('name');
            if ($name !== null && $name !== '') {
                if (! $this->validateName((string) $name)) {
                    return self::FAILURE;
                }

                $attributes['name'] = (string) $name;
            }

            $password = $this->option('password');
            if ($password !== null && $password !== '') {
                $this->warnPasswordOptionExposure();

                if (! $this->validatePassword((string) $password)) {
                    return self::FAILURE;
                }

                $attributes['password'] = Hash::make((string) $password);
            }

            $user->forceFill($attributes)->save();

            $this->components->info('Usuário promovido para administrador superior com sucesso.');

            return self::SUCCESS;
        }

        $name = (string) ($this->option('name') ?: $this->ask('Nome'));

        if (! $this->validateName($name)) {
            return self::FAILURE;
        }

        $password = $this->resolvePassword();

        if (! $this->validatePassword($password)) {
            return self::FAILURE;
        }

        try {
            (new User)->forceFill([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_admin' => true,
                'is_super_admin' => true,
                'role' => 'admin',
            ])->save();
        } catch (UniqueConstraintViolationException) {
            $this->components->error('Já existe um usuário com este e-mail. Execute novamente com --promote para promovê-lo.');

            return self::FAILURE;
        }

        $this->components->info('Administrador superior criado com sucesso.');

        return self::SUCCESS;
    }

    private function resolvePassword(): string
    {
        $password = $this->option('password');

        if ($password !== null && $password !== '') {
            $this->warnPasswordOptionExposure();

            return (string) $password;
        }

        return $this->askForPassword();
    }

    private function validateEmail(string $email): bool
    {
        $validator = Validator::make(
            ['email' => $email],
            ['email' => ['required', 'email', 'max:255']],
            [
                'email.required' => 'Informe o e-mail do administrador.',
                'email.email' => 'Informe um e-mail válido.',
                'email.max' => 'O e-mail deve ter no máximo :max caracteres.',
            ],
        );

        return $this->displayValidationErrors($validator);
    }

    private function validateName(string $name): bool
    {
        $validator = Validator::make(
            ['name' => $name],
            ['name' => ['required', 'string', 'max:255']],
            [
                'name.required' => 'Informe o nome do administrador.',
                'name.string' => 'O nome do administrador deve ser um texto válido.',
                'name.max' => 'O nome do administrador deve ter no máximo :max caracteres.',
            ],
        );

        return $this->displayValidationErrors($validator);
    }

    private function warnPasswordOptionExposure(): void
    {
        $this->components->warn('Atenção: --password pode ficar visível no histórico do shell e na lista de processos. Prefira o modo interativo em produção.');
    }
}
