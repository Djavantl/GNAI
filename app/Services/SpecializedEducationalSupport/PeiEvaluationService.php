<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Pei;
use App\Models\SpecializedEducationalSupport\PeiEvaluation;
use App\Enums\SpecializedEducationalSupport\EvaluationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class PeiEvaluationService
{
    protected $semesterService;

    public function __construct(SemesterService $semesterService)
    {
        $this->semesterService = $semesterService;
    }

    public function index(Pei $pei): Collection
    {
        return $pei->evaluations()
            ->with(['semester', 'professional'])
            ->latest('evaluation_date')
            ->get();
    }

    public function show(PeiEvaluation $evaluation): PeiEvaluation
    {
        return $evaluation->load(['pei.student', 'semester', 'professional']);
    }

    public function create(Pei $pei, array $data): PeiEvaluation
    {
        // Verificação de segurança: Se o $pei não tiver ID, o Route Model Binding falhou
        if (!$pei->id) {
            throw new \Exception('O plano PEI não foi encontrado ou é inválido.');
        }

        return DB::transaction(function () use ($pei, $data) {
            $semester = $this->semesterService->getCurrent();

            if (!$semester) {
                throw new \Exception('Não existe semestre atual configurado no sistema.');
            }

            $evaluationType = $pei->is_finished
                ? EvaluationType::FINAL
                : EvaluationType::PROGRESS;

            if ($evaluationType === EvaluationType::FINAL) {
                $alreadyHasFinal = $pei->evaluations()
                    ->where('evaluation_type', EvaluationType::FINAL->value)
                    ->exists();

                if ($alreadyHasFinal) {
                    throw new \DomainException('Já existe uma avaliação final para este PEI.');
                }
            }

            // Usamos create diretamente no model ou garantimos o pei_id explicitamente
            return PeiEvaluation::create([
                'pei_id'                       => $pei->id,
                'semester_id'                  => $semester->id,
                'evaluation_instruments'       => $data['evaluation_instruments'],
                'parecer'                      => $data['parecer'],
                'successful_proposals'         => $data['successful_proposals'],
                'next_stage_goals'             => $data['next_stage_goals'] ?? null,
                'evaluation_type'              => $evaluationType,
                'evaluation_date'              => now(),
                'evaluated_by_professional_id' => Auth::id(),
            ]);
        });
    }

    public function update(PeiEvaluation $evaluation, array $data): PeiEvaluation
    {
        DB::transaction(function () use ($evaluation, $data) {
            $evaluation->update($data);
        });

        return $evaluation;
    }

    public function delete(PeiEvaluation $evaluation): void
    {
        $evaluation->delete();
    }
}
