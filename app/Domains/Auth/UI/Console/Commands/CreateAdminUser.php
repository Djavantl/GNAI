<?php

declare(strict_types=1);

namespace App\Domains\Auth\UI\Console\Commands;

use App\Domains\Auth\Domain\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

final class CreateAdminUser extends Command
{
    protected $signature = 'auth:create-admin
        {--name= : Nome do administrador. Em promoção, atualiza o nome apenas se informado}
        {--email= : E-mail do administrador}
        {--password= : Senha do administrador. Evite em terminal compartilhado; pode aparecer no histórico/processos}
        {--promote : Promove um usuário existente com o e-mail informado}';

    protected $description = 'Cria o primeiro usuário administrador ou promove um usuário existente.';

    public function handle(): int
    {
        $email = (string) ($this->option('email') ?: $this->ask('E-mail'));

        if (! $this->validateEmail($email)) {
            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->first();

        if ($user instanceof User) {
            if ($user->isAdmin()) {
                $this->components->info('Este usuário já é administrador.');

                return self::SUCCESS;
            }

            if (! $this->option('promote') && ! $this->confirm('Usuário já existe. Promover para administrador?')) {
                $this->components->warn('Operação cancelada.');

                return self::FAILURE;
            }

            $attributes = [
                'is_admin' => true,
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

            $this->components->info('Usuário promovido para administrador com sucesso.');

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
            User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'is_admin' => true,
                'role' => 'admin',
            ]);
        } catch (UniqueConstraintViolationException) {
            $this->components->error('Já existe um usuário com este e-mail. Execute novamente com --promote para promovê-lo.');

            return self::FAILURE;
        }

        $this->components->info('Usuário administrador criado com sucesso.');

        return self::SUCCESS;
    }

    private function resolvePassword(): string
    {
        $password = $this->option('password');

        if ($password !== null && $password !== '') {
            $this->warnPasswordOptionExposure();

            return (string) $password;
        }

        do {
            $password = (string) $this->secret('Senha');
            $confirmation = (string) $this->secret('Confirmar senha');

            if ($password === $confirmation) {
                return $password;
            }

            $this->components->error('As senhas não conferem.');
        } while (true);
    }

    private function validatePassword(string $password): bool
    {
        $validator = Validator::make(
            ['password' => $password],
            [
                'password' => [
                    'required',
                    'string',
                    Password::min(12)
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                        ->uncompromised(),
                ],
            ],
            [
                'password.required' => 'Informe a senha do administrador.',
                'password.min' => 'A senha do administrador deve ter pelo menos :min caracteres.',
                'password.mixed' => 'A senha do administrador deve conter letras maiúsculas e minúsculas.',
                'password.numbers' => 'A senha do administrador deve conter pelo menos um número.',
                'password.symbols' => 'A senha do administrador deve conter pelo menos um símbolo.',
                'password.uncompromised' => 'Esta senha aparece em vazamentos conhecidos. Escolha outra senha.',
            ],
        );

        return $this->displayValidationErrors($validator);
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

    private function displayValidationErrors($validator): bool
    {
        if ($validator->passes()) {
            return true;
        }

        foreach ($validator->errors()->all() as $error) {
            $this->components->error($error);
        }

        return false;
    }

    private function warnPasswordOptionExposure(): void
    {
        $this->components->warn('Atenção: --password pode ficar visível no histórico do shell e na lista de processos. Prefira o modo interativo em produção.');
    }
}
