<?php

declare(strict_types=1);

namespace App\Domains\Audit\Infrastructure;

use App\Domains\Audit\Domain\Models\AuditRecord;
use App\Domains\Auth\Domain\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;

/**
 * A operação em andamento: o uuid que agrupa as mudanças, os dados da requisição para o log técnico e os
 * registros já gravados nela (para fundir gravações seguintes do mesmo registro). Singleton.
 *
 * O middleware web inicia uma operação por requisição; fora dela (comandos, filas), a operação nasce na
 * primeira gravação e vale para o processo.
 */
final class AuditContext
{
    private ?string $operation = null;

    /** @var array<string, mixed> */
    private array $requestData = [];

    /** @var array<string, AuditRecord> por "tipo:id" da entidade */
    private array $operationRecords = [];

    /** @var array<int, User|null> */
    private array $users = [];

    /** @param  array<string, mixed>  $requestData  rota, método, URL, IP, navegador */
    public function start(array $requestData): void
    {
        $this->operation = (string) Str::uuid();
        $this->requestData = $requestData;
        $this->operationRecords = [];
    }

    public function operation(): string
    {
        return $this->operation ??= (string) Str::uuid();
    }

    /** @return array<string, mixed> */
    public function requestData(): array
    {
        return $this->requestData ?: ['origin' => app()->runningInConsole() ? 'console' : 'system'];
    }

    /** Quem age: o usuário autenticado no momento da gravação. Sem usuário, o "Sistema". */
    public function actor(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }

    /** No "entrar como", o administrador que agiu de fato; o ator é o usuário em nome de quem agiu. */
    public function impersonator(): ?User
    {
        try {
            $request = request();
            $id = $request->hasSession() ? $request->session()->get('impersonator_id') : null;
        } catch (Throwable) {
            return null;
        }

        if ($id === null) {
            return null;
        }

        $id = (int) $id;

        return array_key_exists($id, $this->users)
            ? $this->users[$id]
            : $this->users[$id] = User::query()->find($id);
    }

    public function operationRecord(string $entityKey): ?AuditRecord
    {
        return $this->operationRecords[$entityKey] ?? null;
    }

    public function rememberRecord(string $entityKey, AuditRecord $record): void
    {
        $this->operationRecords[$entityKey] = $record;
    }

    public function forgetRecord(string $entityKey): void
    {
        unset($this->operationRecords[$entityKey]);
    }

    /** Uma transação desfeita levou os registros junto: não há mais o que fundir. */
    public function forgetRecords(): void
    {
        $this->operationRecords = [];
    }
}
