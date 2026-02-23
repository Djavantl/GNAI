<?php

namespace App\Services\SpecializedEducationalSupport;

use App\Models\SpecializedEducationalSupport\StudentCourse;
use App\Models\SpecializedEducationalSupport\Student;
use Illuminate\Support\Facades\DB;

class StudentCourseService
{
    /**
     * Lista o histórico de cursos de um aluno específico
     */
    public function getHistoryByStudent(int $studentId, array $filters = [])
    {
        return StudentCourse::query()
            ->with('course')
            ->where('student_id', $studentId)
            ->courseId($filters['course_id'] ?? null)
            ->academicYear($filters['academic_year'] ?? null)
            ->isCurrent($filters['is_current'] ?? null)
            ->orderBy('academic_year', 'desc')
            ->paginate(10) 
            ->withQueryString();
    }

    /**
     * Matricula um aluno (e lida com o histórico/curso atual)
     */
    public function enroll(Student $student, array $data): StudentCourse
    {
        return DB::transaction(function () use ($student, $data) {
            // Se for marcado como atual, desativamos o anterior
            if ($data['is_current'] ?? true) {
                StudentCourse::where('student_id', $student->id)
                    ->where('is_current', true)
                    ->update(['is_current' => false]);
            }

            return StudentCourse::create([
                'student_id'    => $student->id,
                'course_id'     => $data['course_id'],
                'academic_year' => $data['academic_year'],
                'is_current'    => $data['is_current'] ?? false, 
            ]);
        });
    }

    /**
     * Atualiza dados de uma matrícula específica (ex: mudar status para 'completed')
     */
    public function updateEnrollment(StudentCourse $studentCourse, array $data): StudentCourse
    {
        return DB::transaction(function () use ($studentCourse, $data) {
            // Se estiver mudando este registro para 'is_current', desativa os outros
            if (($data['is_current'] ?? false) && !$studentCourse->is_current) {
                StudentCourse::where('student_id', $studentCourse->student_id)
                    ->where('id', '!=', $studentCourse->id)
                    ->update(['is_current' => false]);
            }

            $studentCourse->update($data);
            return $studentCourse;
        });
    }

    public function deleteEnrollment(StudentCourse $studentCourse): void
    {
        $studentCourse->delete();
    } 
}
