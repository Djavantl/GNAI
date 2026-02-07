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

        $alu1 = Student::where('registration', 'ALU001')->first();
        $alu2 = Student::where('registration', 'ALU002')->first();
        $alu3 = Student::where('registration', 'ALU003')->first();


        $courseInfo = Course::where('name', 'Técnico em Informática')->first();
        $courseAdm  = Course::where('name', 'Técnico em Administração')->first();



        if ($alu1 && $courseInfo) {
            StudentCourse::create([
                'student_id' => $alu1->id,
                'course_id' => $courseInfo->id,
                'academic_year' => date('Y'),
                'is_current' => true,
                'status' => 'active',
            ]);
        }

        if ($alu2 && $courseInfo) {
            StudentCourse::create([
                'student_id' => $alu2->id,
                'course_id' => $courseInfo->id,
                'academic_year' => date('Y'),
                'is_current' => true,
                'status' => 'active',
            ]);
        }

        if ($alu3 && $courseAdm) {
            StudentCourse::create([
                'student_id' => $alu3->id,
                'course_id' => $courseAdm->id,
                'academic_year' => date('Y'),
                'is_current' => true,
                'status' => 'active',
            ]);
        }

    }
}
