<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\SpecializedEducationalSupport\Position;

class PSPUSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // People

        $people = [

            // students

            Person::create([
                'name' => 'Marley Teixeira Meira',
                'document' => '28057515074',
                'birth_date' => '2012-01-10',
                'gender' => 'male',
                'email' => 'mxrlrey@gmail.com',
            ]),
            Person::create([
                'name' => 'Djavan Teixeira Lopes',
                'document' => '68425710065',
                'birth_date' => '2011-03-15',
                'gender' => 'male',
                'email' => 'djvnsala2@gmail.com',
            ]),
            Person::create([
                'name' => 'Péricles Caires',
                'document' => '75261909023',
                'birth_date' => '2010-07-20',
                'gender' => 'male',
                'email' => 'djvnsala5@gmail.com',
            ]),
            Person::create([
                'name' => 'Gabriel Rocha',
                'document' => '48668088025',
                'birth_date' => '2010-07-20',
                'gender' => 'male',
                'email' => 'djavansala7@gmail.com',
            ]),
            Person::create([
                'name' => 'Jader Adriel',
                'document' => '98378092054',
                'birth_date' => '2010-07-20',
                'gender' => 'male',
                'email' => 'djavansala8@gmail.com',
            ]),

            // professionals

            Person::create([
                'name' => 'Adriany',
                'document' => '59271956010',
                'birth_date' => '1990-05-10',
                'gender' => 'female',
                'email' => 'adriany.prof@teste.com',
            ]),
            Person::create([
                'name' => 'Marta',
                'document' => '98800858090',
                'birth_date' => '1988-08-22',
                'gender' => 'male',
                'email' => 'marta.prof@teste.com',
            ]),
            Person::create([
                'name' => 'Paula Mendes',
                'document' => '97849285077',
                'birth_date' => '1992-11-30',
                'gender' => 'female',
                'email' => 'paula.prof@teste.com',
            ]),
        ];

        // Students (primeiras 3 pessoas)

        Student::create([
            'person_id' => $people[0]->id,
            'registration' => 'ALU001',
            'entry_date' => now(),
        ]);

        Student::create([
            'person_id' => $people[1]->id,
            'registration' => 'ALU002',
            'entry_date' => now(),
        ]);

        Student::create([
            'person_id' => $people[2]->id,
            'registration' => 'ALU003',
            'entry_date' => now(),
        ]);

        Student::create([
            'person_id' => $people[3]->id,
            'registration' => 'ALU004',
            'entry_date' => now(),
        ]);

        Student::create([
            'person_id' => $people[4]->id,
            'registration' => 'ALU005',
            'entry_date' => now(),
        ]);

        // Professionals (últimas 3 pessoas)

        $prof1 = Professional::create([
            'person_id' => $people[5]->id,
            'position_id' => 1,
            'registration' => 'PROF001',
            'entry_date' => now(),
        ]);

        $prof2 = Professional::create([
            'person_id' => $people[6]->id,
            'position_id' => 2,
            'registration' => 'PROF002',
            'entry_date' => now(),
        ]);

        $prof3 = Professional::create([
            'person_id' => $people[7]->id,
            'position_id' => 1,
            'registration' => 'PROF003',
            'entry_date' => now(),
        ]);

        // Logins (SÓ profissionais)

        User::create([
            'name' => $people[5]->name,
            'email' => $people[5]->email,
            'password' => Hash::make('napne2026'),
            'role' => 'professional',
            'professional_id' => $prof1->id,
        ]);

        User::create([
            'name' => $people[6]->name,
            'email' => $people[6]->email,
            'password' => Hash::make('napne2026'),
            'role' => 'professional',
            'professional_id' => $prof2->id,
        ]);

        User::create([
            'name' => $people[7]->name,
            'email' => $people[7]->email,
            'password' => Hash::make('napne2026'),
            'role' => 'professional',
            'professional_id' => $prof3->id,
        ]);
    }
}
