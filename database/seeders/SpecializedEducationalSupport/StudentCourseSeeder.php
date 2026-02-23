<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\StudentCourse;

class StudentCourseSeeder extends Seeder
{
    public function run(): void
    {
        // Pegamos os cursos disponíveis
        $courseInfo = Course::where('name', 'Técnico em Informática')->first();
        $courseAdm  = Course::where('name', 'Técnico em Administração')->first();
        
        // Se não existirem, pegamos os dois primeiros que encontrar
        if (!$courseInfo || !$courseAdm) {
            $courses = Course::limit(2)->get();
            $courseInfo = $courses->first();
            $courseAdm = $courses->last();
        }

        $students = Student::all();

        foreach ($students as $index => $student) {
            // Alterna entre Informática e Administração baseado no ID (par/ímpar)
            $courseId = ($index % 2 == 0) ? $courseInfo->id : $courseAdm->id;

            // Evita duplicados caso a seeder rode duas vezes
            StudentCourse::updateOrCreate(
                ['student_id' => $student->id, 'course_id' => $courseId],
                [
                    'academic_year' => 2026,
                    'is_current' => true,
                ]
            );
        }
    }
}