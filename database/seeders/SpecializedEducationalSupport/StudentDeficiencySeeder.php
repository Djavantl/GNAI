<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Support\Facades\DB;

class StudentDeficiencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();
        $deficiencies = Deficiency::all();

        if ($students->isEmpty() || $deficiencies->count() < 2) {
            throw new \Exception('É necessário ter alunos e pelo menos 2 deficiências cadastradas.');
        }

        foreach ($students as $student) {

            // Sorteia 2 deficiências diferentes
            $selectedDeficiencies = $deficiencies->random(2);

            foreach ($selectedDeficiencies as $deficiency) {
                DB::table('students_deficiencies')->insert([
                    'student_id' => $student->id,
                    'deficiency_id' => $deficiency->id,
                    'severity' => collect(['mild', 'moderate', 'severe'])->random(),
                    'uses_support_resources' => (bool) random_int(0, 1),
                    'notes' => 'Deficiência identificada e acompanhada pelo AEE.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
