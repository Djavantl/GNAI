<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $studentsData = [
            ['name' => 'Ana Beatriz Oliveira', 'email' => 'ana.beatriz@escola.com', 'gender' => 'female', 'doc' => '10010010011', 'birth' => '2013-05-12'],
            ['name' => 'Bruno Henrique Souza', 'email' => 'bruno.henrique@escola.com', 'gender' => 'male', 'doc' => '20020020022', 'birth' => '2012-08-25'],
            ['name' => 'Carla Dias Martins', 'email' => 'carla.dias@escola.com', 'gender' => 'female', 'doc' => '30030030033', 'birth' => '2014-01-15'],
            ['name' => 'Daniel Ferreira Lima', 'email' => 'daniel.ferreira@escola.com', 'gender' => 'male', 'doc' => '40040040044', 'birth' => '2011-11-30'],
            ['name' => 'Eduarda Costa Silva', 'email' => 'eduarda.costa@escola.com', 'gender' => 'female', 'doc' => '50050050055', 'birth' => '2013-03-22'],
            ['name' => 'Felipe Augusto Rocha', 'email' => 'felipe.augusto@escola.com', 'gender' => 'male', 'doc' => '60060060066', 'birth' => '2012-07-08'],
            ['name' => 'Giovanna Mendes Vaz', 'email' => 'giovanna.mendes@escola.com', 'gender' => 'female', 'doc' => '70070070077', 'birth' => '2014-09-18'],
            ['name' => 'Hugo Leonardo Gomes', 'email' => 'hugo.leonardo@escola.com', 'gender' => 'male', 'doc' => '80080080088', 'birth' => '2010-12-05'],
            ['name' => 'Isabela Santos Reis', 'email' => 'isabela.santos@escola.com', 'gender' => 'female', 'doc' => '90090090099', 'birth' => '2013-10-10'],
            ['name' => 'João Pedro Almeida', 'email' => 'joao.pedro@escola.com', 'gender' => 'male', 'doc' => '11011011011', 'birth' => '2012-02-28'],
        ];

        foreach ($studentsData as $index => $data) {
            // 1. Criar a Pessoa
            $person = Person::create([
                'name' => $data['name'],
                'document' => $data['doc'],
                'birth_date' => $data['birth'],
                'gender' => $data['gender'],
                'email' => $data['email'],
            ]);

            // 2. Criar o Aluno vinculado à Pessoa
            Student::create([
                'person_id' => $person->id,
                'registration' => 'ALU' . str_pad($index + 4, 3, '0', STR_PAD_LEFT), // Começa do ALU004
                'entry_date' => now()->subMonths(rand(1, 12)), // Data de ingresso aleatória no último ano
                'status' => 'active'
            ]);
        }
    }
}