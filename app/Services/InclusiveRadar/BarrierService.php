<?php

namespace App\Services\InclusiveRadar;

use App\Enums\InclusiveRadar\{BarrierStatus, InspectionType};
use App\Models\InclusiveRadar\Barrier;
use Illuminate\Support\Facades\{Auth, DB};

class BarrierService
{
    public function __construct(
        protected InspectionService $inspectionService
    ) {}

    /**
     * RF: cadastra uma barreira com saneamento de contexto e inspeção inicial.
     * Uso: criação de ocorrências no mapa inclusivo.
     */
    public function store(array $data): Barrier
    {
        return DB::transaction(
            fn() => $this->persist(new Barrier(), $data)
        );
    }

    /**
     * RF: atualiza a barreira preservando contexto do relator e histórico de inspeção.
     * Uso: manutenção operacional de ocorrências registradas no radar.
     */
    public function update(Barrier $barrier, array $data): Barrier
    {
        return DB::transaction(
            fn() => $this->persist($barrier, $data)
        );
    }

    /**
     * RF: remove a barreira em transação única.
     * Uso: exclusão administrativa de registros que podem ser descartados.
     */
    public function delete(Barrier $barrier): void
    {
        DB::transaction(fn() => $barrier->delete());
    }

    /**
     * RF: centraliza a persistência da barreira e seus vínculos auxiliares.
     * Uso: fluxo comum compartilhado entre criação e atualização.
     */
    protected function persist(Barrier $barrier, array $data): Barrier
    {
        $data = $this->sanitizeReporterData($data);
        $data = $this->prepareData($barrier, $data);

        $barrier->fill($data)->save();

        $this->syncRelations($barrier, $data);
        $this->handleInspectionLog($barrier, $data);

        return $barrier->fresh([
            'category',
            'location',
            'deficiencies'
        ]);
    }

    /**
     * RF: completa campos derivados antes da persistência da barreira.
     * Uso: preparação de dados operacionais do cadastro.
     */
    protected function prepareData(Barrier $barrier, array $data): array
    {
        if (!$barrier->exists && Auth::check()) {
            $data['registered_by_user_id'] = Auth::id();
        }

        if (!empty($data['no_location'])) {
            $data['location_id'] = null;
        }

        return $data;
    }

    /**
     * RF: normaliza os dados do relator conforme o tipo de identificação informado.
     * Uso: evitar combinações inválidas entre anonimato, texto livre e vínculos internos.
     */
    protected function sanitizeReporterData(array $data): array
    {
        $cleanFields = [
            'affected_student_id' => null,
            'affected_professional_id' => null,
            'affected_person_name' => null,
            'affected_person_role' => null,
            'is_anonymous' => false,
            'not_applicable' => false,
        ];

        if (!empty($data['is_anonymous'])) {
            return array_merge($data, $cleanFields, ['is_anonymous' => true]);
        }

        if (!empty($data['not_applicable'])) {
            return array_merge($data, $cleanFields, [
                'not_applicable' => true,
                'affected_person_name' => $data['affected_person_name'] ?? null,
                'affected_person_role' => $data['affected_person_role'] ?? null,
            ]);
        }

        return array_merge($data, [
            'is_anonymous' => false,
            'not_applicable' => false,
            'affected_person_name' => null,
            'affected_person_role' => null,
            'affected_student_id' => $data['affected_student_id'] ?? null,
            'affected_professional_id' => $data['affected_professional_id'] ?? null,
        ]);
    }

    /**
     * RF: sincroniza os relacionamentos editáveis da barreira.
     * Uso: atualização do público-alvo associado à ocorrência.
     */
    protected function syncRelations(Barrier $barrier, array $data): void
    {
        if (isset($data['deficiencies'])) {
            $barrier->deficiencies()->sync($data['deficiencies']);
        }
    }

    /**
     * RF: registra ou evita inspeções conforme a relevância da alteração feita.
     * Uso: manutenção do histórico operacional sem gerar logs vazios.
     */
    protected function handleInspectionLog(Barrier $barrier, array $data): void
    {
        $isUpdate = $barrier->wasRecentlyCreated === false;
        $oldStatus = $isUpdate ? $barrier->latestStatus()?->value : null;
        $newStatus = $data['status'] ?? $oldStatus ?? BarrierStatus::IDENTIFIED->value;

        $statusChanged = $isUpdate && $newStatus !== $oldStatus;
        $hasInteraction = filled($data['inspection_description'] ?? null) || !empty($data['images']);

        if (in_array($newStatus, [BarrierStatus::RESOLVED->value, BarrierStatus::NOT_APPLICABLE->value])) {
            $barrier->update(['resolved_at' => $barrier->resolved_at ?? now()]);
        } else {
            $barrier->update(['resolved_at' => null]);
        }

        if ($isUpdate && !$statusChanged && !$hasInteraction) {
            return;
        }

        $this->inspectionService->createForModel($barrier, [
            'state' => null,
            'status' => $newStatus,
            'inspection_date' => $data['inspection_date'] ?? now(),
            'type' => $data['inspection_type'] ?? ($isUpdate ? InspectionType::PERIODIC->value : InspectionType::INITIAL->value),
            'description' => $data['inspection_description'] ?? ($isUpdate ? null : 'Registro inicial da barreira.'),
            'images' => $data['images'] ?? [],
        ]);
    }
}
