<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Position;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfessionalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $professionalsData = [
            ['name' => 'Ricardo Alves', 'gender' => 'male', 'pos' => 'Psicólogo'],
            ['name' => 'Fernanda Lima', 'gender' => 'female', 'pos' => 'Assistente Social'],
            ['name' => 'Roberto Carlos', 'gender' => 'male', 'pos' => 'Fonoaudiólogo'],
            ['name' => 'Camila Pitanga', 'gender' => 'female', 'pos' => 'Intérprete de Libras'],
            ['name' => 'Marcos Palmeira', 'gender' => 'male', 'pos' => 'Terapeuta Ocupacional'],
            ['name' => 'Glória Pires', 'gender' => 'female', 'pos' => 'Coordenador do NAPNE'],
            ['name' => 'Antônio Fagundes', 'gender' => 'male', 'pos' => 'Professor AEE'],
            ['name' => 'Mariana Ximenes', 'gender' => 'female', 'pos' => 'Psicólogo'],
            ['name' => 'Lázaro Ramos', 'gender' => 'male', 'pos' => 'Professor AEE'],
            ['name' => 'Taís Araújo', 'gender' => 'female', 'pos' => 'Assistente Social'],
        ];

        foreach ($professionalsData as $index => $data) {
            // 1. Criar a Pessoa
            $person = Person::create([
                'name' => $data['name'],
                'document' => '777' . str_pad($index, 8, '0', STR_PAD_LEFT), // Gera docs únicos simples
                'birth_date' => now()->subYears(rand(25, 50))->format('Y-m-d'),
                'gender' => $data['gender'],
                'email' => strtolower(str_replace(' ', '.', $data['name'])) . '@napne.com',
            ]);

            // 2. Buscar o ID do cargo pelo nome definido na PositionSeeder
            $position = Position::where('name', $data['pos'])->first();

            // 3. Criar o Profissional
            $professional = Professional::create([
                'person_id' => $person->id,
                'position_id' => $position->id ?? 1, // Fallback para ID 1 se não achar
                'registration' => 'PROF' . str_pad($index + 4, 3, '0', STR_PAD_LEFT), // Continua do PROF004
                'entry_date' => now()->subMonths(rand(1, 24)),
            ]);

            // 4. Criar o Usuário de acesso
            User::create([
                'name' => $person->name,
                'email' => $person->email,
                'password' => Hash::make('napne2026'),
                'role' => 'professional',
                'professional_id' => $professional->id,
            ]);
        }
    }
}