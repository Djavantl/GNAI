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
        // Usuários que podem criar pendências
        $users = User::where('role', 'professional')->get();

        // Profissionais que recebem pendências
        $professionals = Professional::all();

        if ($users->isEmpty() || $professionals->isEmpty()) {
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

        // Somente as prioridades permitidas
        $priorities = ['low', 'medium', 'high'];

        foreach ($professionals as $index => $professional) {

            // Alterna o criador entre os usuários
            $creator = $users[$index % $users->count()];

            $titleIndex = array_rand($titles);

            Pendency::create([
                'created_by'   => $creator->id,
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