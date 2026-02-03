<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\Pei;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentContext;
use App\Models\SpecializedEducationalSupport\Semester;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PeiService
{
    public function store(Student $student): Pei
    {
        return DB::transaction(function () use ($student) {
            $currentContext = $student->contexts()
                ->where('is_current', true)
                ->first();

            if (!$currentContext) {
                throw new \Exception("O aluno não possui um Contexto Educacional ativo.");
            }

            $currentSemester = Semester::where('is_current', true)->first();

            $data = [
                'student_id'         => $student->id,
                'professional_id'    => Auth::id(), 
                'student_context_id' => $currentContext->id,
                'semester_id'        => $currentSemester ? $currentSemester->id : null,
                'is_finished'        => false,
            ];

            return Pei::create($data);
        });
    }

    public function show(Pei $pei): Pei
    {
        // Carrega o contexto (histórico/necessidades) e as disciplinas vinculadas [cite: 54, 76]
        return $pei->load(['studentContext', 'adaptations.evaluation']);
    }

    public function finish(Pei $pei): bool
    {
        // Marca o plano como concluído para arquivamento no setor de registros [cite: 59]
        return $pei->update(['is_finished' => true]);
    }
}