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
        $users = User::where('role', 'professional')->get();
        $professionals = Professional::all();

        if ($users->isEmpty() || $professionals->isEmpty()) {
            return;
        }

        Pendency::create([
            'created_by'   => $users[0]->id,
            'assigned_to'  => $professionals[1]->id,
            'title'        => 'Atualizar relatório de aluno do AEE',
            'description'  => 'Revisar e atualizar o relatório de progresso do AEE para os alunos atribuídos.',
            'priority'     => 'urgent',
            'due_date'     => now()->addDays(3),
            'is_completed' => false,
        ]);

        Pendency::create([
            'created_by'   => $users[1]->id,
            'assigned_to'  => $professionals[0]->id,
            'title'        => 'Preparar reunião pedagógica',
            'description'  => 'Organizar materiais e pauta para a próxima reunião pedagógica.',
            'priority'     => 'high',
            'due_date'     => now()->addDays(7),
            'is_completed' => false,
        ]);

        Pendency::create([
            'created_by'   => $users[2]->id,
            'assigned_to'  => $professionals[2]->id,
            'title'        => 'Atualizar registros de frequência dos alunos',
            'description'  => 'Garantir que todos os dados de frequência estejam registrados corretamente no sistema.',
            'priority'     => 'medium',
            'due_date'     => now()->addDays(10),
            'is_completed' => true,
        ]);

        Pendency::create([
            'created_by'   => $users[0]->id,
            'assigned_to'  => $professionals[0]->id,
            'title'        => 'Revisar planos de ensino individualizados',
            'description'  => 'Verificar e atualizar os planos de ensino individualizados de acordo com as novas avaliações.',
            'priority'     => 'low',
            'due_date'     => now()->addDays(15),
            'is_completed' => false,
        ]);
    }
}