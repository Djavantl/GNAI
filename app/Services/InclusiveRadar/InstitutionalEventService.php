<?php

namespace App\Services\InclusiveRadar;

use App\Models\InclusiveRadar\InstitutionalEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class InstitutionalEventService
{
    public function store(array $data): InstitutionalEvent
    {
        return DB::transaction(
            fn() => $this->persist(new InstitutionalEvent(), $data)
        );
    }

    public function update(InstitutionalEvent $event, array $data): InstitutionalEvent
    {
        return DB::transaction(
            fn() => $this->persist($event, $data)
        );
    }

    public function delete(InstitutionalEvent $event): void
    {
        DB::transaction(function () use ($event) {
            $event->delete();
        });
    }

    protected function persist(InstitutionalEvent $event, array $data): InstitutionalEvent
    {
        $this->validateEventDates($data);

        $this->saveModel($event, $data);

        return $event->fresh();
    }

    private function saveModel(InstitutionalEvent $event, array $data): void
    {
        $event->fill($data)->save();
    }

    /**
     * Valida regras de negócio relacionadas a datas e horários
     */
    private function validateEventDates(array $data): void
    {
        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $startTime = Carbon::createFromFormat('H:i', $data['start_time']);
        $endTime = Carbon::createFromFormat('H:i', $data['end_time']);

        // Data final não pode ser antes da inicial
        if ($endDate->lt($startDate)) {
            throw ValidationException::withMessages([
                'end_date' => 'A data de término não pode ser anterior à data de início.',
            ]);
        }

        // Se a data final for o mesmo dia da inicial, hora final deve ser maior que a inicial
        if ($startDate->eq($endDate) && $endTime->lte($startTime)) {
            throw ValidationException::withMessages([
                'end_time' => 'O horário de término deve ser maior que o horário de início para o mesmo dia.',
            ]);
        }
    }
}
