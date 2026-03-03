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
        // Pegamos todos os usuários (quem cria) e todos os profissionais (quem recebe)
        $users = User::where('role', 'professional')->get();
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

        $priorities = ['urgent', 'high', 'medium', 'low'];

        foreach ($professionals as $index => $professional) {
            
            // Definimos quem será o criador (usamos o índice para variar os usuários criadores)
            $creator = $users[$index % $users->count()];

            for ($i = 1; $i <= 5; $i++) {
                
                $titleIndex = array_rand($titles);
                
                Pendency::create([
                    'created_by'   => $creator->id,
                    'assigned_to'  => $professional->id,
                    'title'        => $titles[$titleIndex] . " (#$i)",
                    'description'  => "Tarefa detalhada referente a " . strtolower($titles[$titleIndex]) . " para o fluxo de trabalho do NAPNE.",
                    'priority'     => $priorities[array_rand($priorities)],
                    'due_date'     => now()->addDays(rand(1, 30)),
                    'is_completed' => (rand(1, 10) > 8), // 20% de chance de já estar concluída
                    'created_at'   => now()->subDays(rand(1, 5)),
                    'updated_at'   => now(),
                ]);
            }
        }
    }
}