<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\WaitlistStatus;
use App\Models\InclusiveRadar\Loan;
use App\Models\InclusiveRadar\Waitlist;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WaitlistService
{
    /*
    |--------------------------------------------------------------------------
    | GESTÃO DE SOLICITAÇÕES (CRUD)
    |--------------------------------------------------------------------------
    | Métodos para entrada, edição e remoção de registros na fila de espera.
    */

    /**
     * REGISTRO DE ENTRADA NA FILA
     * * IMPORTÂNCIA: Gerencia a demanda reprimida quando não há estoque físico.
     * * FLUXO: Bloqueia o item para garantir leitura real do estoque (lockForUpdate),
     * valida se o beneficiário já não possui o item e registra a data da solicitação
     * para garantir a prioridade (FIFO - First In, First Out).
     */
    public function store(array $data): Waitlist
    {
        return DB::transaction(function () use ($data) {
            $item = $data['waitlistable_type']::lockForUpdate()
                ->findOrFail($data['waitlistable_id']);

            $data['waitlistable_type'] = $item->getMorphClass();

            $this->validateNewWaitlist($item, $data);

            return Waitlist::create([
                'waitlistable_id'   => $item->id,
                'waitlistable_type' => $data['waitlistable_type'],
                'student_id'        => $data['student_id'] ?? null,
                'professional_id'   => $data['professional_id'] ?? null,
                'user_id'           => $data['user_id'],
                'requested_at'      => now(),
                'status'            => WaitlistStatus::WAITING->value,
                'observation'       => $data['observation'] ?? null,
            ]);
        });
    }

    /**
     * ATUALIZAÇÃO DE SOLICITAÇÃO
     * * IMPORTÂNCIA: Permite mudar o status ou adicionar notas administrativas.
     * * REGRA: Impede mudanças de status em solicitações já resolvidas (Atendidas/Canceladas).
     */
    public function update(Waitlist $waitlist, array $data): Waitlist
    {
        $this->validateStatusModification($waitlist, $data);

        $waitlist->update($this->filterUpdatableFields($data));

        return $waitlist->fresh();
    }

    /**
     * REMOÇÃO DE REGISTRO
     * * SEGURANÇA: Bloqueia a exclusão de solicitações que já foram atendidas (Fulfilled)
     * para preservar o histórico de atendimento do sistema.
     */
    public function delete(Waitlist $waitlist): void
    {
        $this->validateDeletion($waitlist);
        $waitlist->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | FLUXO DE ATENDIMENTO (BUSINESS ACTIONS)
    |--------------------------------------------------------------------------
    | Ações que movem o beneficiário através do ciclo da fila.
    */

    /**
     * CANCELAMENTO VOLUNTÁRIO
     * * IMPORTÂNCIA: Permite que o beneficiário desista da fila sem que isso
     * conte como um item entregue.
     */
    public function cancel(Waitlist $waitlist): Waitlist
    {
        $currentStatus = WaitlistStatus::tryFrom($waitlist->status);

        if ($currentStatus !== WaitlistStatus::WAITING) {
            throw ValidationException::withMessages([
                'status' => 'Apenas solicitações em espera podem ser canceladas.'
            ]);
        }

        $waitlist->update(['status' => WaitlistStatus::CANCELLED->value]);

        return $waitlist->fresh();
    }

    /**
     * NOTIFICAÇÃO DE DISPONIBILIDADE
     * * IMPORTÂNCIA: Seleciona o próximo da fila baseado na ordem cronológica (oldest).
     * * GATILHO: Geralmente chamado quando um item retorna do empréstimo ou manutenção.
     */
    public function notifyNext($item): ?Waitlist
    {
        $next = Waitlist::where('waitlistable_id', $item->id)
            ->where('waitlistable_type', $item->getMorphClass())
            ->where('status', WaitlistStatus::WAITING->value)
            ->oldest('requested_at')
            ->first();

        if (!$next) return null;

        $next->update(['status' => WaitlistStatus::NOTIFIED->value]);

        return $next->fresh();
    }

    /**
     * FINALIZAÇÃO DE ATENDIMENTO
     * * IMPORTÂNCIA: Marca que o recurso foi efetivamente entregue ao solicitante.
     */
    public function fulfill(Waitlist $waitlist): Waitlist
    {
        $waitlist->update(['status' => WaitlistStatus::FULFILLED->value]);

        return $waitlist->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | REGRAS DE INTEGRIDADE (VALIDAÇÕES)
    |--------------------------------------------------------------------------
    */

    /**
     * VALIDAÇÃO DE ENTRADA NA FILA
     * * LÓGICA: Impede a entrada se houver estoque disponível (o correto seria
     * emprestar direto) ou se o beneficiário já estiver na fila/com o item.
     */
    private function validateNewWaitlist($item, array $data): void
    {
        $this->ensureNoStockAvailable($item);
        $this->ensureNoDuplicateEntry($item, $data);
    }

    /**
     * VERIFICAÇÃO DE SALDO DISPONÍVEL
     * * IMPORTÂNCIA: Garante que a fila de espera só seja usada para recursos esgotados.
     */
    private function ensureNoStockAvailable($item): void
    {
        $status = $item->resourceStatus;

        if (!$status?->blocks_loan && $item->quantity_available > 0) {
            throw ValidationException::withMessages([
                'waitlistable_id' => 'Este recurso ainda possui unidades disponíveis e pode ser emprestado, portanto não é possível criar uma fila de espera.'
            ]);
        }
    }

    /**
     * BLOQUEIO DE DUPLICIDADE
     * * IMPORTÂNCIA: Evita que um mesmo aluno ocupe várias posições na fila para
     * o mesmo item ou peça algo que ele já possui em mãos.
     */
    private function ensureNoDuplicateEntry($item, array $data): void
    {
        $student = $data['student_id'] ?? null;
        $professional = $data['professional_id'] ?? null;

        $existsQuery = Waitlist::where('waitlistable_id', $item->id)
            ->where('waitlistable_type', $item->getMorphClass())
            ->whereIn('status', [
                WaitlistStatus::WAITING->value,
                WaitlistStatus::NOTIFIED->value
            ]);

        if ($student) $existsQuery->where('student_id', $student);
        else $existsQuery->where('professional_id', $professional);

        if ($existsQuery->exists()) {
            throw ValidationException::withMessages([
                $student ? 'student_id' : 'professional_id' =>
                    'Este beneficiário já possui uma solicitação ativa para este recurso.'
            ]);
        }

        $loanQuery = Loan::where('loanable_id', $item->id)
            ->where('loanable_type', $item->getMorphClass())
            ->whereNull('return_date');

        if ($student) $loanQuery->where('student_id', $student);
        else $loanQuery->where('professional_id', $professional);

        if ($loanQuery->exists()) {
            throw ValidationException::withMessages([
                $student ? 'student_id' : 'professional_id' =>
                    'Este beneficiário já possui um empréstimo ativo deste recurso.'
            ]);
        }
    }

    /**
     * VALIDAÇÃO DE MODIFICAÇÃO DE STATUS
     * * REGRA: Protege registros finalizados contra alterações acidentais.
     */
    private function validateStatusModification(Waitlist $waitlist, array $data): void
    {
        if (!isset($data['status'])) return;

        $currentStatus = WaitlistStatus::tryFrom($waitlist->status);

        $updatableKeys = array_keys($data);
        $onlyObservation = count($updatableKeys) === 1 && in_array('observation', $updatableKeys);

        if (!$onlyObservation && in_array($currentStatus, [WaitlistStatus::FULFILLED, WaitlistStatus::CANCELLED], true)) {
            throw ValidationException::withMessages([
                'status' => 'Solicitação já finalizada não pode ser alterada, exceto observações.'
            ]);
        }
    }

    /**
     * VALIDAÇÃO DE EXCLUSÃO
     * * REGRA: Mantém a integridade do histórico de atendimentos realizados.
     */
    private function validateDeletion(Waitlist $waitlist): void
    {
        $currentStatus = WaitlistStatus::tryFrom($waitlist->status);

        if ($currentStatus === WaitlistStatus::FULFILLED) {
            throw ValidationException::withMessages([
                'status' => 'Solicitações já atendidas não podem ser removidas.'
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MÉTODOS AUXILIARES
    |--------------------------------------------------------------------------
    */

    /**
     * FILTRO DE CAMPOS PERMITIDOS
     */
    private function filterUpdatableFields(array $data): array
    {
        return collect($data)
            ->only(['status', 'observation'])
            ->toArray();
    }
}
