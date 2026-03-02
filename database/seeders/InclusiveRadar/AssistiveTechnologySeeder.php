<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\ConservationState;

class AssistiveTechnologySeeder extends Seeder
{
    public function run(): void
    {
//        // Garantir deficiências cadastradas
//        $this->ensureDeficienciesExist();
//
//        // Status "available"
//        $availableStatusId = DB::table('resource_statuses')
//            ->where('code', 'available')
//            ->value('id');
//
//        if (!$availableStatusId) {
//            $this->command->error('Status "available" não encontrado. Execute primeiro o ResourceStatusSeeder.');
//            return;
//        }
//
//        // Usuário para as inspeções
//        $userId = $this->ensureUserExists();
//
//        // IDs das deficiências
//        $deficiencyIds = DB::table('deficiencies')->pluck('id')->toArray();
//
//        // Gerar 15 notebooks no formato "Notebook (marca) (cor)"
//        $notebookBrands = ['Dell', 'Lenovo', 'HP', 'Acer', 'Samsung', 'Apple', 'Asus', 'Positivo', 'Vaio', 'Microsoft'];
//        $colors = ['Prata', 'Preto', 'Cinza', 'Branco', 'Azul', 'Vermelho', 'Dourado'];
//
//        $notebookNames = [];
//        for ($i = 0; $i < 15; $i++) {
//            $brand = $notebookBrands[array_rand($notebookBrands)];
//            $color = $colors[array_rand($colors)];
//            $notebookNames[] = "Notebook {$brand} {$color}";
//        }
//
//        $allowedStates = ['novo', 'bom', 'regular'];
//        $now = Carbon::now();
//
//        foreach ($notebookNames as $name) {
//            // Notebooks são sempre físicos
//            $isDigital = false;
//            $assetCode = 'PAT-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
//            $conservationState = $allowedStates[array_rand($allowedStates)];
//            $notes = $this->generateNotes($name, $conservationState, $isDigital);
//
//            // Criar a tecnologia
//            $technology = AssistiveTechnology::create([
//                'name' => $name,
//                'is_digital' => $isDigital,
//                'notes' => $notes,
//                'asset_code' => $assetCode,
//                'quantity' => 1,
//                'quantity_available' => 1,
//                'conservation_state' => $conservationState,
//                'status_id' => $availableStatusId,
//                'is_active' => true,
//            ]);
//
//            // Associar deficiências (1 a 3 aleatórias)
//            $this->attachDeficiencies($technology, $deficiencyIds);
//
//            // Criar inspeções via relação
//            $this->createInspections($technology, $conservationState, $userId);
//        }
//
//        $this->command->info("15 notebooks criados com sucesso, cada um com inspeções e deficiências associadas.");
//    }
//
//    private function ensureDeficienciesExist(): void
//    {
//        if (DB::table('deficiencies')->count() > 0) {
//            return;
//        }
//
//        DB::table('deficiencies')->insert([
//            ['name' => 'Visual', 'cid_code' => 'H54', 'description' => 'Deficiência visual, incluindo baixa visão e cegueira parcial ou total.'],
//            ['name' => 'Auditiva', 'cid_code' => 'H90', 'description' => 'Perda auditiva parcial ou total, podendo ser unilateral ou bilateral.'],
//            ['name' => 'Física', 'cid_code' => 'G80', 'description' => 'Comprometimentos motores que afetam mobilidade, coordenação ou força física.'],
//            ['name' => 'Intelectual', 'cid_code' => 'F70', 'description' => 'Limitações significativas no funcionamento intelectual e no comportamento adaptativo.'],
//            ['name' => 'Psicossocial', 'cid_code' => 'F32', 'description' => 'Condições que afetam o comportamento, emoção e interação social do indivíduo.'],
//        ]);
//    }
//
//    private function ensureUserExists(): int
//    {
//        $user = DB::table('users')->first();
//        if ($user) {
//            return $user->id;
//        }
//
//        return DB::table('users')->insertGetId([
//            'name' => 'Admin Seeder',
//            'email' => 'admin@seeder.com',
//            'password' => bcrypt('password'),
//            'created_at' => now(),
//            'updated_at' => now(),
//        ]);
//    }
//
//    private function attachDeficiencies(AssistiveTechnology $technology, array $deficiencyIds): void
//    {
//        if (empty($deficiencyIds)) {
//            return;
//        }
//
//        $numberOfDeficiencies = rand(1, min(3, count($deficiencyIds)));
//        $selectedDeficiencies = array_rand(array_flip($deficiencyIds), $numberOfDeficiencies);
//
//        if (!is_array($selectedDeficiencies)) {
//            $selectedDeficiencies = [$selectedDeficiencies];
//        }
//
//        $technology->deficiencies()->sync($selectedDeficiencies);
//    }
//
//    private function generateNotes(string $name, string $state, bool $isDigital): string
//    {
//        $notes = [
//            'novo'    => 'Equipamento novo, acabado de chegar ao acervo. Embalagem original lacrada.',
//            'bom'     => 'Em bom estado, com pequenos sinais de uso decorrentes de empréstimos anteriores.',
//            'regular' => 'Apresenta desgaste visível pelo uso frequente, mas ainda totalmente funcional.',
//        ];
//
//        $base = $notes[$state] ?? 'Tecnologia assistiva disponível para empréstimo.';
//        $base .= ' Notebook adaptado com tecnologias assistivas.';
//
//        return $base;
//    }
//
//    private function createInspections(AssistiveTechnology $technology, string $currentState, int $userId): void
//    {
//        $numInspections = rand(1, 4);
//        $acquisitionDate = Carbon::now()->subMonths(rand(6, 24))->subDays(rand(0, 30));
//
//        // Inspeção inicial
//        $technology->inspections()->create([
//            'state'           => $currentState,
//            'inspection_date' => $acquisitionDate->format('Y-m-d'),
//            'description'     => $this->generateInspectionDescription('initial', $technology->name, $currentState, $technology->is_digital),
//            'type'            => InspectionType::INITIAL->value,
//            'user_id'         => $userId,
//            'created_at'      => $acquisitionDate,
//            'updated_at'      => $acquisitionDate,
//        ]);
//
//        if ($numInspections > 1) {
//            $lastDate = clone $acquisitionDate;
//            for ($i = 2; $i <= $numInspections; $i++) {
//                $type = $this->getRandomInspectionType($i);
//                $lastDate = $lastDate->copy()->addMonths(rand(2, 8))->addDays(rand(0, 15));
//
//                if ($lastDate->isFuture()) {
//                    $lastDate = Carbon::now()->subDays(rand(1, 10));
//                }
//
//                $inspectionState = $this->determineInspectionState($currentState, $i);
//
//                $technology->inspections()->create([
//                    'state'           => $inspectionState,
//                    'inspection_date' => $lastDate->format('Y-m-d'),
//                    'description'     => $this->generateInspectionDescription($type, $technology->name, $inspectionState, $technology->is_digital),
//                    'type'            => $type,
//                    'user_id'         => $userId,
//                    'created_at'      => $lastDate,
//                    'updated_at'      => $lastDate,
//                ]);
//            }
//        }
//    }
//
//    private function getRandomInspectionType(int $index): string
//    {
//        $types = ['periodic', 'maintenance', 'return'];
//        if ($index == 2) {
//            return (rand(1, 100) <= 70) ? 'periodic' : 'maintenance';
//        }
//        return $types[array_rand($types)];
//    }
//
//    private function determineInspectionState(string $currentState, int $inspectionIndex): string
//    {
//        $rand = rand(1, 100);
//        if ($rand <= 70) {
//            return $currentState;
//        } elseif ($rand <= 90) {
//            return $this->improveState($currentState);
//        } else {
//            return $this->worsenState($currentState);
//        }
//    }
//
//    private function improveState(string $state): string
//    {
//        return match($state) {
//            'regular' => 'bom',
//            'bom'     => 'novo',
//            'novo'    => 'novo',
//            default   => 'bom',
//        };
//    }
//
//    private function worsenState(string $state): string
//    {
//        return match($state) {
//            'novo'    => 'bom',
//            'bom'     => 'regular',
//            'regular' => 'regular',
//            default   => 'regular',
//        };
//    }
//
//    private function generateInspectionDescription(string $type, string $technologyName, string $state, bool $isDigital): string
//    {
//        $descriptions = [
//            'initial' => [
//                'novo'    => 'Vistoria inicial de aquisição. Tecnologia recém-chegada, lacrada, sem qualquer sinal de uso.',
//                'bom'     => 'Vistoria inicial. Equipamento em bom estado, com leves marcas de uso anterior, mas totalmente funcional.',
//                'regular' => 'Vistoria inicial. Recurso já apresenta alguns sinais de desgaste, porém ainda atende plenamente.',
//            ],
//            'periodic' => [
//                'novo'    => 'Inspeção periódica: recurso mantém-se em perfeitas condições, sem necessidade de intervenção.',
//                'bom'     => 'Inspeção periódica: pequenos desgastes esperados, mas funcionamento normal. Recomendada limpeza.',
//                'regular' => 'Inspeção periódica: desgaste natural acentuado, mas operacional. Avaliar necessidade de manutenção.',
//            ],
//            'maintenance' => [
//                'novo'    => 'Manutenção preventiva realizada: limpeza, calibração e verificação geral. Estado mantido.',
//                'bom'     => 'Manutenção corretiva: ajuste em componentes móveis. Após reparo, encontra-se em bom estado.',
//                'regular' => 'Manutenção: substituição de peças desgastadas. Após intervenção, ainda regular, mas com vida útil prolongada.',
//            ],
//            'return' => [
//                'novo'    => 'Devolução de empréstimo: recurso foi utilizado e retornou em perfeito estado, conforme inspeção.',
//                'bom'     => 'Retorno de empréstimo: usuário devolveu com pequenos sinais de uso, nada que comprometa.',
//                'regular' => 'Devolução: recurso apresenta desgaste além do esperado, será avaliado para possível manutenção.',
//            ],
//        ];
//
//        return $descriptions[$type][$state] ?? "Inspeção do tipo $type realizada. Estado: $state.";
    }
}
