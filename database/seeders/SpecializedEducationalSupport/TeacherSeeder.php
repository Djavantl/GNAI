<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Teacher;
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\Discipline;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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

            // 1. Criar a Pessoa
            $person = Person::create([
                'name' => $data['name'],
                'document' => '888' . str_pad($index, 8, '0', STR_PAD_LEFT),
                'birth_date' => now()->subYears(rand(28, 55))->format('Y-m-d'),
                'gender' => $data['gender'],
                'email' => strtolower(str_replace(' ', '.', $data['name'])) . '@escola.com',
            ]);

            // 2. Criar o Professor
            $teacher = Teacher::create([
                'person_id' => $person->id,
                'registration' => 'DOC' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
            ]);

            // 3. Vincular ao curso
            $course = Course::where('name', $data['course'])->first();
            if ($course) {
                $teacher->courses()->syncWithoutDetaching([$course->id]);

                // opcional → vincula automaticamente disciplinas do curso
                $disciplines = $course->disciplines()->pluck('disciplines.id')->toArray();
                if (!empty($disciplines)) {
                    $teacher->disciplines()->syncWithoutDetaching($disciplines);
                }
            }

            // 4. Criar usuário para login
            User::create([
                'name' => $person->name,
                'email' => $person->email,
                'password' => Hash::make('napne2026'),
                'role' => 'teacher',
                'teacher_id' => $teacher->id,
            ]);
        }
    }
}