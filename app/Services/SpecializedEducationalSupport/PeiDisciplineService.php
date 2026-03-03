<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Pei;
use App\Models\SpecializedEducationalSupport\PeiDiscipline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PeiDisciplineService
{
    /**
     * Verifica se o PEI está finalizado e lança exceção se estiver.
     * Isso centraliza a regra de "Somente Leitura".
     */
    private function checkPeiStatus(Pei $pei)
    {
        if ($pei->is_finished) {
            throw new Exception("Este PEI já foi finalizado e não permite alterações.");
        }
    }

    public function listByPei(Pei $pei)
    {
        // Visualização é permitida mesmo finalizado
        return $pei->disciplines()->with(['teacher', 'discipline', 'creator'])->get();
    }

    public function store(Pei $pei, array $data): PeiDiscipline
    {
        $this->checkPeiStatus($pei);

        return DB::transaction(function () use ($pei, $data) {
            return PeiDiscipline::create([
                'pei_id' => $pei->id,
                'teacher_id' => $data['teacher_id'],
                'discipline_id' => $data['discipline_id'],
                'creator_id' => Auth::id(),
                'specific_objectives' => $data['specific_objectives'],
                'content_programmatic' => $data['content_programmatic'],
                'methodologies' => $data['methodologies'],
                'evaluations' => $data['evaluations'],
            ]);
        });
    }

    public function update(PeiDiscipline $peiDiscipline, array $data): PeiDiscipline
    {
        // Verifica o status através do relacionamento com o PEI pai
        $this->checkPeiStatus($peiDiscipline->pei);

        return DB::transaction(function () use ($peiDiscipline, $data) {
            $peiDiscipline->update([
                'teacher_id' => $data['teacher_id'],
                'discipline_id' => $data['discipline_id'],
                'specific_objectives' => $data['specific_objectives'],
                'content_programmatic' => $data['content_programmatic'],
                'methodologies' => $data['methodologies'],
                'evaluations' => $data['evaluations'],
            ]);

            return $peiDiscipline;
        });
    }

    public function delete(PeiDiscipline $peiDiscipline): bool
    {
        $this->checkPeiStatus($peiDiscipline->pei);

        return $peiDiscipline->delete();
    }

    public function find(PeiDiscipline $peiDiscipline): PeiDiscipline
    {
        // Visualização permitida
        return $peiDiscipline->load(['teacher', 'discipline', 'pei', 'creator']);
    }
}