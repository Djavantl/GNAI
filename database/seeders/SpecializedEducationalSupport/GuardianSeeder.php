<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Guardian;

class GuardianSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();

        $lastNames = ['Silva', 'Oliveira', 'Santos', 'Pereira', 'Costa', 'Almeida'];

        foreach ($students as $student) {
            // Pegamos o último nome do aluno para criar um pai/mãe fictício
            $studentLastName = explode(' ', $student->person->name);
            $lastName = end($studentLastName);
            
            // Dados do Responsável
            $gender = (rand(0, 1) == 0) ? 'female' : 'male';
            $firstName = ($gender == 'female') ? 'Maria' : 'José';
            $relationship = ($gender == 'female') ? 'Mãe' : 'Pai';

            // 1. Criar a Pessoa do Responsável
            $person = Person::create([
                'name' => "$firstName $lastName " . $lastNames[array_rand($lastNames)],
                'document' => str_pad(rand(0, 99999999999), 11, '0', STR_PAD_LEFT),
                'birth_date' => now()->subYears(rand(35, 50))->format('Y-m-d'),
                'gender' => $gender,
                'email' => strtolower($firstName) . $student->id . "@email.com",
            ]);

            // 2. Criar o Vínculo de Responsável
            Guardian::create([
                'student_id' => $student->id,
                'person_id' => $person->id,
                'relationship' => $relationship,
            ]);
        }
    }
}