<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Pendency;
use App\Models\User;
use App\Models\SpecializedEducationalSupport\Professional;

class PendencySeeder extends Seeder
{
    public function run(): void
    {
        // Pega apenas usuários que possuem um perfil de profissional vinculado
        // e já carrega a relação para evitar múltiplas queries (Eager Loading)
        $usersWithProfessional = User::whereHas('professional')
            ->with('professional')
            ->get();

        // Profissionais que recebem pendências
        $professionals = Professional::all();

        if ($usersWithProfessional->isEmpty() || $professionals->isEmpty()) {
            return;
        }

        $titles = [
            'Revisar PEI semestral',
            'Atualizar anamnese de aluno novo',
            'Lançar frequência da semana',
            'Preparar material adaptado para aula',
            'Reunião de feedback com responsáveis',
            'Relatório de evolução trimestral',
            'Solicitar renovação de laudo médico',
            'Organizar oficina de tecnologia assistiva',
            'Ajustar cronograma de atendimentos',
            'Digitalizar documentos de matrícula'
        ];

        $priorities = ['low', 'medium', 'high'];

        foreach ($professionals as $index => $professional) {
            // Seleciona um usuário que tem perfil profissional
            $userCreator = $usersWithProfessional[$index % $usersWithProfessional->count()];
            
            // Pega o ID do Profissional vinculado a esse usuário
            $creatorProfessionalId = $userCreator->professional->id;

            $titleIndex = array_rand($titles);

            Pendency::create([
                'created_by'   => $creatorProfessionalId, // Agora usa o ID do profissional
                'assigned_to'  => $professional->id,
                'title'        => $titles[$titleIndex],
                'description'  => "Tarefa referente a " . strtolower($titles[$titleIndex]) . ".",
                'priority'     => $priorities[array_rand($priorities)],
                'due_date'     => now()->addDays(rand(1, 30)),
                'is_completed' => false,
                'created_at'   => now()->subDays(rand(1, 5)),
                'updated_at'   => now(),
            ]);
        }
    }
}