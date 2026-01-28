<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class BarrierStatusSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $statuses = [
            [
                'name' => 'Identificada',
                'description' => 'Barreira identificada, aguardando análise ou encaminhamento.',
            ],
            [
                'name' => 'Em Análise',
                'description' => 'Barreira em processo de análise técnica ou pedagógica.',
            ],
            [
                'name' => 'Em Tratamento',
                'description' => 'Ações de correção ou mitigação estão em andamento.',
            ],
            [
                'name' => 'Resolvida',
                'description' => 'Barreira solucionada com sucesso.',
            ],
            [
                'name' => 'Não Aplicável',
                'description' => 'Barreira não se aplica ao contexto informado.',
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('barrier_statuses')->updateOrInsert(
                ['name' => $status['name']],
                array_merge($status, [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
