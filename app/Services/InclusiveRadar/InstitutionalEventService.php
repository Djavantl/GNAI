<?php

namespace App\Services\InclusiveRadar;

use App\Exceptions\BusinessRuleException;
use App\Models\InclusiveRadar\InstitutionalEvent;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InstitutionalEventService
{
    /**
     * RF: cria um evento institucional validando cronologia e duração.
     * Uso: agenda do radar e disparo de lembretes automáticos.
     */
    public function store(array $data): InstitutionalEvent
    {
        return DB::transaction(
            fn() => $this->persist(new InstitutionalEvent(), $data)
        );
    }

    /**
     * RF: atualiza o evento mantendo as mesmas validações temporais do cadastro.
     * Uso: edição da agenda institucional do módulo.
     */
    public function update(InstitutionalEvent $event, array $data): InstitutionalEvent
    {
        return DB::transaction(
            fn() => $this->persist($event, $data)
        );
    }

    /**
     * RF: remove o evento institucional em transação única.
     * Uso: exclusão administrativa de compromissos da agenda.
     */
    public function delete(InstitutionalEvent $event): void
    {
        DB::transaction(function () use ($event) {
            $event->delete();
        });
    }

    /**
     * RF: centraliza a persistência do evento após aplicar as regras de agenda.
     * Uso: fluxo compartilhado entre criação e atualização.
     */
    protected function persist(InstitutionalEvent $event, array $data): InstitutionalEvent
    {
        $this->validateEventDates($data);

        $this->saveModel($event, $data);

        return $event->fresh();
    }

    /**
     * RF: salva o estado final do evento já validado.
     * Uso: etapa isolada de persistência do fluxo de agenda.
     */
    private function saveModel(InstitutionalEvent $event, array $data): void
    {
        $event->fill($data)->save();
    }

    /**
     * RF: valida coerência entre datas e horários do evento institucional.
     * Uso: impedir intervalos inválidos no calendário e nos lembretes automáticos.
     */
    private function validateEventDates(array $data): void
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $startTime = Carbon::createFromFormat('H:i', $data['start_time']);
        $endTime = Carbon::createFromFormat('H:i', $data['end_time']);

        if ($endDate->lt($startDate)) {
            throw new BusinessRuleException('A data de término não pode ser anterior à data de início.');
        }

        if ($startDate->eq($endDate) && $endTime->lte($startTime)) {
            throw new BusinessRuleException('O horário de término deve ser maior que o horário de início para o mesmo dia.');
        }
    }
}
