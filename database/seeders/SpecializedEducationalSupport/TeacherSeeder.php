<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Teacher;
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\TeacherCourseDiscipline;
use App\Models\User;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teachersData = [
            ['name' => 'Carlos Eduardo Lima', 'gender' => 'male', 'course' => 'Técnico em Informática'],
            ['name' => 'Juliana Martins', 'gender' => 'female', 'course' => 'Técnico em Administração'],
            ['name' => 'Ricardo Gomes', 'gender' => 'male', 'course' => 'Técnico em Mecânica'],
            ['name' => 'Fernanda Souza', 'gender' => 'female', 'course' => 'Técnico em Informática'],
        ];

        foreach ($teachersData as $index => $data) {
            DB::transaction(function () use ($data, $index) {
                $person = Person::firstOrCreate(
                    ['document' => '888' . str_pad($index, 8, '0', STR_PAD_LEFT)],
                    [
                        'name'       => $data['name'],
                        'birth_date' => now()->subYears(rand(28, 55))->format('Y-m-d'),
                        'gender'     => $data['gender'],
                        'email'      => strtolower(str_replace(' ', '.', $data['name'])) . '@escola.com',
                    ]
                );

                $teacher = Teacher::firstOrCreate(
                    ['person_id' => $person->id],
                    [
                        'registration' => 'DOC' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                    ]
                );
                $course = Course::where('name', $data['course'])->first();

                if ($course) {
                    // Vínculo professor -> curso
                    $teacher->courses()->syncWithoutDetaching([$course->id]);

                    // Pega algumas disciplinas reais do curso
                    $disciplineIds = $course->disciplines()
                        ->orderBy('name')
                        ->limit(7)
                        ->pluck('disciplines.id')
                        ->toArray();

                    // Vínculo professor -> curso -> disciplina
                    foreach ($disciplineIds as $disciplineId) {
                        TeacherCourseDiscipline::firstOrCreate([
                            'teacher_id'    => $teacher->id,
                            'course_id'     => $course->id,
                            'discipline_id' => $disciplineId,
                        ]);
                    }
                }

                User::firstOrCreate(
                    ['email' => $person->email],
                    [
                        'name'       => $person->name,
                        'password'   => Hash::make('napne2026'),
                        'role'       => 'teacher',
                        'teacher_id' => $teacher->id,
                    ]
                );
            });
        }
    }
}