<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\WaitlistStatus;
use App\Exceptions\BusinessRuleException;
use App\Models\InclusiveRadar\Loan;
use App\Models\InclusiveRadar\Waitlist;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

class WaitlistService
{
    /**
     * RF: cria uma solicitação de fila de espera com lock e resolução do morph alias.
     * Uso: registro de interesse quando o item está indisponível para empréstimo.
     */
    public function store(array $data): Waitlist
    {
        return DB::transaction(function () use ($data) {
            $modelClass = Relation::getMorphedModel($data['waitlistable_type'])
                ?? $data['waitlistable_type'];

            $item = $modelClass::lockForUpdate()->findOrFail($data['waitlistable_id']);

            $data['waitlistable_type'] = $item->getMorphClass();

            $this->validateNewWaitlist($item, $data);

            return Waitlist::create([
                'waitlistable_id' => $item->id,
                'waitlistable_type' => $data['waitlistable_type'],
                'student_id' => $data['student_id'] ?? null,
                'professional_id' => $data['professional_id'] ?? null,
                'user_id' => $data['user_id'],
                'requested_at' => now(),
                'status' => WaitlistStatus::WAITING->value,
                'observation' => $data['observation'] ?? null,
            ]);
        });
    }

    /**
     * RF: atualiza apenas os campos permitidos da solicitação de fila.
     * Uso: ajustes administrativos de status e observação.
     */
    public function update(Waitlist $waitlist, array $data): Waitlist
    {
        $this->validateStatusModification($waitlist, $data);

        $waitlist->update($this->filterUpdatableFields($data));

        return $waitlist->fresh();
    }

    /**
     * RF: remove a solicitação quando o status ainda permite exclusão.
     * Uso: limpeza administrativa de registros não atendidos.
     */
    public function delete(Waitlist $waitlist): void
    {
        $this->validateDeletion($waitlist);
        $waitlist->delete();
    }

    /**
     * RF: cancela uma solicitação ainda pendente de atendimento.
     * Uso: desistência explícita do aluno ou profissional na fila.
     */
    public function cancel(Waitlist $waitlist): Waitlist
    {
        $currentStatus = WaitlistStatus::tryFrom($waitlist->status);

        if ($currentStatus !== WaitlistStatus::WAITING) {
            throw new BusinessRuleException('Apenas solicitações em espera podem ser canceladas.');
        }

        $waitlist->update(['status' => WaitlistStatus::CANCELLED->value]);

        return $waitlist->fresh();
    }

    /**
     * RF: promove o próximo da fila quando o item volta a ficar disponível.
     * Uso: notificação automática de beneficiários em espera.
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
     * RF: centraliza as validações obrigatórias antes de entrar na fila.
     * Uso: criação de solicitações de espera do módulo.
     */
    private function validateNewWaitlist($item, array $data): void
    {
        $this->validateBeneficiary($data);
        $this->ensureNoStockAvailable($item);
        $this->ensureNoDuplicateEntry($item, $data);
    }

    /**
     * RF: exige um único beneficiário válido para a solicitação de espera.
     * Uso: integridade do atendimento e prevenção de ambiguidade operacional.
     */
    private function validateBeneficiary(array $data): void
    {
        $student = $data['student_id'] ?? null;
        $professional = $data['professional_id'] ?? null;

        if (empty($student) && empty($professional)) {
            throw new BusinessRuleException('É necessário informar um aluno ou um profissional.');
        }

        if (!empty($student) && !empty($professional)) {
            throw new BusinessRuleException('Não é permitido informar aluno e profissional ao mesmo tempo.');
        }
    }

    /**
     * RF: impede fila de espera quando o item ainda pode ser emprestado normalmente.
     * Uso: forçar o fluxo direto de empréstimo enquanto houver disponibilidade real.
     */
    private function ensureNoStockAvailable($item): void
    {
        $status = $item->status;

        if (!$status->blocksLoan() && $item->quantity_available > 0) {
            throw new BusinessRuleException('Este recurso ainda possui unidades disponíveis e pode ser emprestado, portanto não é possível criar uma fila de espera.');
        }
    }

    /**
     * RF: bloqueia duplicidade entre fila ativa e empréstimo ativo do mesmo item.
     * Uso: evitar reservas redundantes para um mesmo beneficiário.
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
            throw new BusinessRuleException('Este beneficiário já possui uma solicitação ativa para este recurso.');
        }

        $loanQuery = Loan::where('loanable_id', $item->id)
            ->where('loanable_type', $item->getMorphClass())
            ->whereNull('return_date');

        if ($student) $loanQuery->where('student_id', $student);
        else $loanQuery->where('professional_id', $professional);

        if ($loanQuery->exists()) {
            throw new BusinessRuleException('Este beneficiário já possui um empréstimo ativo deste recurso.');
        }
    }

    /**
     * RF: trava alterações estruturais em solicitações já finalizadas.
     * Uso: preservação do histórico de atendimento da fila.
     */
    private function validateStatusModification(Waitlist $waitlist, array $data): void
    {
        if (!isset($data['status'])) return;

        $currentStatus = WaitlistStatus::tryFrom($waitlist->status);

        $updatableKeys = array_keys($data);
        $onlyObservation = count($updatableKeys) === 1 && in_array('observation', $updatableKeys);

        if (!$onlyObservation && in_array($currentStatus, [WaitlistStatus::FULFILLED, WaitlistStatus::CANCELLED], true)) {
            throw new BusinessRuleException('Solicitação já finalizada não pode ser alterada, exceto observações.');
        }
    }

    /**
     * RF: impede remoção de solicitações já atendidas.
     * Uso: manter rastreabilidade mínima do fluxo de fila.
     */
    private function validateDeletion(Waitlist $waitlist): void
    {
        $currentStatus = WaitlistStatus::tryFrom($waitlist->status);

        if ($currentStatus === WaitlistStatus::FULFILLED) {
            throw new BusinessRuleException('Solicitações já atendidas não podem ser removidas.');
        }
    }

    /**
     * RF: limita o update aos campos operacionais permitidos pela regra de negócio.
     * Uso: saneamento de payload em atualizações de fila de espera.
     */
    private function filterUpdatableFields(array $data): array
    {
        return collect($data)
            ->only(['status', 'observation'])
            ->toArray();
    }
}
