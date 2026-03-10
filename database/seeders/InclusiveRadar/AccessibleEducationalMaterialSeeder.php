<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\ResourceStatus;

class AccessibleEducationalMaterialSeeder extends Seeder
{
    public function run(): void
    {
        // Garantir que deficiências existam
        $this->ensureDeficienciesExist();

        // Garantir que recursos de acessibilidade existam
        $this->ensureAccessibilityFeaturesExist();

        // Usar o enum diretamente
        $status = ResourceStatus::AVAILABLE->value; // 'available'

        // Usuário para as inspeções
        $userId = $this->ensureUserExists();

        // IDs das deficiências e recursos de acessibilidade
        $deficiencyIds = DB::table('deficiencies')->pluck('id')->toArray();
        $featureIds = DB::table('accessibility_features')->pluck('id')->toArray();

        // Lista de materiais pedagógicos acessíveis (exemplos)
        $materials = [
            ['name' => 'Livro Didático em Braille', 'digital' => false],
            ['name' => 'Apostila com Fonte Ampliada', 'digital' => false],
            ['name' => 'Vídeo com Libras e Legendas', 'digital' => true],
            ['name' => 'Áudio-descrição de Obra Literária', 'digital' => true],
            ['name' => 'Mapa Tátil', 'digital' => false],
            ['name' => 'Jogo Pedagógico Adaptado', 'digital' => false],
            ['name' => 'Software Leitor de Tela', 'digital' => true],
            ['name' => 'Livro em Áudio (MP3)', 'digital' => true],
            ['name' => 'Modelo 3D de Estrutura Celular', 'digital' => false],
            ['name' => 'Texto em Comunicação Alternativa (PECS)', 'digital' => false],
            ['name' => 'Aplicativo de Alfabetização com Libras', 'digital' => true],
            ['name' => 'Cartilha em Relevo', 'digital' => false],
            ['name' => 'Vídeo com Janela de Libras', 'digital' => true],
            ['name' => 'Maquete Tátil de Relevo', 'digital' => false],
            ['name' => 'Material Didático em Contraste', 'digital' => false],
        ];

        $allowedStates = ['novo', 'bom', 'regular'];

        foreach ($materials as $material) {
            $isDigital = $material['digital'];
            $assetCode = $isDigital ? null : 'PAT-MPA-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $conservationState = $isDigital ? 'naoaplicavel' : $allowedStates[array_rand($allowedStates)];
            $notes = $this->generateNotes($material['name'], $conservationState, $isDigital);
            $quantity = $isDigital ? 999 : rand(1, 5);
            $quantityAvailable = $quantity;

            // Criar o material
            $mpa = AccessibleEducationalMaterial::create([
                'name' => $material['name'],
                'is_digital' => $isDigital,
                'notes' => $notes,
                'asset_code' => $assetCode,
                'quantity' => $quantity,
                'quantity_available' => $quantityAvailable,
                'conservation_state' => $conservationState,
                'status' => $status,
                'is_active' => true,
            ]);

            // Associar deficiências (1 a 3)
            $this->attachDeficiencies($mpa, $deficiencyIds);

            // Associar recursos de acessibilidade (1 a 4)
            $this->attachAccessibilityFeatures($mpa, $featureIds);

            // Criar inspeções (apenas se for físico)
            if (!$isDigital) {
                $this->createInspections($mpa, $conservationState, $userId);
            } else {
                // Para digitais, criar apenas uma inspeção inicial simbólica
                $this->createDigitalInspection($mpa, $userId);
            }
        }

        $this->command->info(count($materials) . " materiais pedagógicos acessíveis criados com sucesso.");
    }

    private function ensureDeficienciesExist(): void
    {
        if (DB::table('deficiencies')->count() > 0) {
            return;
        }

        DB::table('deficiencies')->insert([
            ['name' => 'Visual', 'cid_code' => 'H54', 'description' => 'Deficiência visual, incluindo baixa visão e cegueira parcial ou total.'],
            ['name' => 'Auditiva', 'cid_code' => 'H90', 'description' => 'Perda auditiva parcial ou total, podendo ser unilateral ou bilateral.'],
            ['name' => 'Física', 'cid_code' => 'G80', 'description' => 'Comprometimentos motores que afetam mobilidade, coordenação ou força física.'],
            ['name' => 'Intelectual', 'cid_code' => 'F70', 'description' => 'Limitações significativas no funcionamento intelectual e no comportamento adaptativo.'],
            ['name' => 'Psicossocial', 'cid_code' => 'F32', 'description' => 'Condições que afetam o comportamento, emoção e interação social do indivíduo.'],
        ]);
    }

    private function ensureAccessibilityFeaturesExist(): void
    {
        if (DB::table('accessibility_features')->count() > 0) {
            return;
        }

        DB::table('accessibility_features')->insert([
            ['name' => 'Braille', 'description' => 'Texto em braille.'],
            ['name' => 'Áudio-descrição', 'description' => 'Narração descritiva de imagens e cenas.'],
            ['name' => 'Libras', 'description' => 'Língua Brasileira de Sinais em vídeo.'],
            ['name' => 'Letra Ampliada', 'description' => 'Fonte ampliada para baixa visão.'],
            ['name' => 'Contraste', 'description' => 'Alto contraste para melhor visualização.'],
            ['name' => 'Áudio (MP3)', 'description' => 'Versão em áudio do conteúdo.'],
            ['name' => 'Legenda', 'description' => 'Legendas em vídeos.'],
            ['name' => 'Comunicação Alternativa', 'description' => 'Símbolos e imagens para comunicação.'],
            ['name' => 'Relevo', 'description' => 'Elementos táteis em relevo.'],
            ['name' => 'Text-to-Speech', 'description' => 'Leitura por sintetizador de voz.'],
        ]);
    }

    private function ensureUserExists(): int
    {
        $user = DB::table('users')->first();
        if ($user) {
            return $user->id;
        }

        return DB::table('users')->insertGetId([
            'name' => 'Admin Seeder',
            'email' => 'admin@seeder.com',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function attachDeficiencies(AccessibleEducationalMaterial $mpa, array $deficiencyIds): void
    {
        if (empty($deficiencyIds)) {
            return;
        }

        $number = rand(1, min(3, count($deficiencyIds)));
        $selected = array_rand(array_flip($deficiencyIds), $number);
        if (!is_array($selected)) {
            $selected = [$selected];
        }

        $mpa->deficiencies()->sync($selected);
    }

    private function attachAccessibilityFeatures(AccessibleEducationalMaterial $mpa, array $featureIds): void
    {
        if (empty($featureIds)) {
            return;
        }

        $number = rand(1, min(4, count($featureIds)));
        $selected = array_rand(array_flip($featureIds), $number);
        if (!is_array($selected)) {
            $selected = [$selected];
        }

        $mpa->accessibilityFeatures()->sync($selected);
    }

    private function generateNotes(string $name, string $state, bool $isDigital): string
    {
        if ($isDigital) {
            return "Material digital disponível para download e acesso remoto. Não se aplica estado de conservação.";
        }

        $notes = [
            'novo' => 'Material novo, recém-adquirido, em perfeitas condições.',
            'bom' => 'Em bom estado, com pequenos sinais de uso, mas totalmente legível/utilizável.',
            'regular' => 'Apresenta desgaste visível (ex.: páginas amareladas, capa levemente danificada), mas conteúdo íntegro.',
        ];

        return $notes[$state] ?? 'Material pedagógico acessível disponível para empréstimo.';
    }

    private function createInspections(AccessibleEducationalMaterial $mpa, string $currentState, int $userId): void
    {
        $numInspections = rand(1, 3);
        $acquisitionDate = Carbon::now()->subMonths(rand(6, 24))->subDays(rand(0, 30));

        // Inspeção inicial
        $mpa->inspections()->create([
            'state' => $currentState,
            'inspection_date' => $acquisitionDate->format('Y-m-d'),
            'description' => $this->generateInspectionDescription('initial', $mpa->name, $currentState),
            'type' => InspectionType::INITIAL->value,
            'user_id' => $userId,
            'created_at' => $acquisitionDate,
            'updated_at' => $acquisitionDate,
        ]);

        if ($numInspections > 1) {
            $lastDate = clone $acquisitionDate;
            for ($i = 2; $i <= $numInspections; $i++) {
                $type = $this->getRandomInspectionType($i);
                $lastDate = $lastDate->copy()->addMonths(rand(3, 8))->addDays(rand(0, 15));

                if ($lastDate->isFuture()) {
                    $lastDate = Carbon::now()->subDays(rand(1, 10));
                }

                $inspectionState = $this->determineInspectionState($currentState, $i);

                $mpa->inspections()->create([
                    'state' => $inspectionState,
                    'inspection_date' => $lastDate->format('Y-m-d'),
                    'description' => $this->generateInspectionDescription($type, $mpa->name, $inspectionState),
                    'type' => $type,
                    'user_id' => $userId,
                    'created_at' => $lastDate,
                    'updated_at' => $lastDate,
                ]);
            }
        }
    }

    private function createDigitalInspection(AccessibleEducationalMaterial $mpa, int $userId): void
    {
        $mpa->inspections()->create([
            'state' => ConservationState::NOT_APPLICABLE->value,
            'inspection_date' => now()->format('Y-m-d'),
            'description' => 'Material digital – inspeção simbólica de disponibilidade.',
            'type' => InspectionType::INITIAL->value,
            'user_id' => $userId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function getRandomInspectionType(int $index): string
    {
        $types = ['periodic', 'maintenance', 'return'];
        if ($index == 2) {
            return (rand(1, 100) <= 70) ? 'periodic' : 'maintenance';
        }
        return $types[array_rand($types)];
    }

    private function determineInspectionState(string $currentState, int $inspectionIndex): string
    {
        $rand = rand(1, 100);
        if ($rand <= 70) {
            return $currentState;
        } elseif ($rand <= 90) {
            return $this->improveState($currentState);
        } else {
            return $this->worsenState($currentState);
        }
    }

    private function improveState(string $state): string
    {
        return match ($state) {
            'regular' => 'bom',
            'bom' => 'novo',
            'novo' => 'novo',
            default => 'bom',
        };
    }

    private function worsenState(string $state): string
    {
        return match ($state) {
            'novo' => 'bom',
            'bom' => 'regular',
            'regular' => 'regular',
            default => 'regular',
        };
    }

    private function generateInspectionDescription(string $type, string $materialName, string $state): string
    {
        $descriptions = [
            'initial' => [
                'novo' => 'Vistoria inicial: material novo, sem qualquer dano.',
                'bom' => 'Vistoria inicial: material em bom estado, com leves marcas de uso.',
                'regular' => 'Vistoria inicial: material já apresenta desgaste visível.',
            ],
            'periodic' => [
                'novo' => 'Inspeção periódica: mantém-se em perfeitas condições.',
                'bom' => 'Inspeção periódica: pequenos desgastes, mas ainda adequado.',
                'regular' => 'Inspeção periódica: desgaste acentuado, recomenda-se monitoramento.',
            ],
            'maintenance' => [
                'novo' => 'Manutenção preventiva: limpeza e verificação geral.',
                'bom' => 'Manutenção: reparo de pequenos danos, recuperado.',
                'regular' => 'Manutenção: intervenção para prolongar vida útil.',
            ],
            'return' => [
                'novo' => 'Devolução: material retornou em perfeito estado.',
                'bom' => 'Devolução: material utilizado, retornou com pequenos sinais.',
                'regular' => 'Devolução: material danificado, necessita avaliação.',
            ],
        ];

        return $descriptions[$type][$state] ?? "Inspeção do tipo $type. Estado: $state.";
    }
}
