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
        $positions = Position::query()
            ->whereIn('name', [
                'Professor(a) AEE',
                'Coordenador(a) do NAPNE',
                'Secretário(a)',
                'Assessoria Pedagógica',
                'Tutor(a) de Pares',
            ])
            ->get()
            ->keyBy('name');

        // People

        $people = [

            // students

            Person::create([
                'name' => 'Luis Soares',
                'document' => '28057515074',
                'birth_date' => '2012-01-10',
                'gender' => 'male',
                'email' => 'luis@gmail.com',
            ]),
            Person::create([
                'name' => 'Deyverson Neves',
                'document' => '68425710065',
                'birth_date' => '2011-03-15',
                'gender' => 'male',
                'email' => 'deyverson@gmail.com',
            ]),
            Person::create([
                'name' => 'Cauan Castro',
                'document' => '75261909023',
                'birth_date' => '2010-07-20',
                'gender' => 'male',
                'email' => 'cauan@gmail.com',
            ]),
            Person::create([
                'name' => 'Cleiton Araújo',
                'document' => '48668088025',
                'birth_date' => '2010-07-20',
                'gender' => 'male',
                'email' => 'cleiton@gmail.com',
            ]),
            Person::create([
                'name' => 'Gustavo Natan',
                'document' => '98378092054',
                'birth_date' => '2010-07-20',
                'gender' => 'male',
                'email' => 'gustavo@gmail.com',
            ]),

            // professionals

            Person::create([
                'name' => 'Professor(a) AEE',
                'document' => '59271956010',
                'birth_date' => '1990-05-10',
                'gender' => 'female',
                'email' => 'prof.aee@napne.com',
            ]),
            Person::create([
                'name' => 'Cordenador(a)',
                'document' => '98800858090',
                'birth_date' => '1988-08-22',
                'gender' => 'male',
                'email' => 'coordenacao@napne.com',
            ]),
            Person::create([
                'name' => 'Secretario(a)',
                'document' => '97849285077',
                'birth_date' => '1992-11-30',
                'gender' => 'female',
                'email' => 'secretaria@napne.com',
            ]),
            Person::create([
                'name' => 'Assessor(a) Pedagógico(a)',
                'document' => '35885807000',
                'birth_date' => '2000-02-23',
                'gender' => 'not_specified',
                'email' => 'assessoria@napne.com',
            ]),
            Person::create([
                'name' => 'Tutor de Pares',
                'document' => '95218291099',
                'birth_date' => '2005-08-17',
                'gender' => 'not_specified',
                'email' => 'tutor@napne.com',
            ]),
        ];

        // Students (primeiras 5 pessoas)

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

        // Professionals

        $prof1 = Professional::create([
            'person_id' => $people[5]->id,
            'position_id' => $positions->get('Professor(a) AEE')?->id ?? 1,
            'registration' => 'PROF001',
            'entry_date' => now(),
        ]);

        $prof2 = Professional::create([
            'person_id' => $people[6]->id,
            'position_id' => $positions->get('Coordenador(a) do NAPNE')?->id ?? 1,
            'registration' => 'PROF002',
            'entry_date' => now(),
        ]);

        $prof3 = Professional::create([
            'person_id' => $people[7]->id,
            'position_id' => $positions->get('Secretário(a)')?->id ?? 1,
            'registration' => 'PROF003',
            'entry_date' => now(),
        ]);

        $prof4 = Professional::create([
            'person_id' => $people[8]->id,
            'position_id' => $positions->get('Assessoria Pedagógica')?->id ?? 1,
            'registration' => 'PROFI002',
            'entry_date' => now(),
        ]);

        $prof5 = Professional::create([
            'person_id' => $people[9]->id,
            'position_id' => $positions->get('Tutor(a) de Pares')?->id ?? 1,
            'registration' => 'TUTOR001',
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

        User::create([
            'name' => $people[8]->name,
            'email' => $people[8]->email,
            'password' => Hash::make('napne2026'),
            'role' => 'professional',
            'professional_id' => $prof4->id,
        ]);

        User::create([
            'name' => $people[9]->name,
            'email' => $people[9]->email,
            'password' => Hash::make('napne2026'),
            'role' => 'professional',
            'professional_id' => $prof5->id,
        ]);
    }
}
