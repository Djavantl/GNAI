<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\InspectionType;

class AssistiveTechnologySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $model = new AssistiveTechnology();
        $modelClass = get_class($model);
        $morphType = array_search($modelClass, Relation::morphMap(), true) ?: $modelClass;

        // Mapeamentos
        $types = DB::table('resource_types')->where('for_assistive_technology', true)->pluck('id', 'name');
        $statuses = DB::table('resource_statuses')->where('for_assistive_technology', true)->pluck('id', 'code');
        $deficiencias = DB::table('deficiencies')->pluck('id', 'name');
        $attributes = DB::table('type_attributes')->get(['id', 'name'])->keyBy(fn($a) => strtolower($a->name));
        $users = DB::table('users')->pluck('id');
        $userId = $users->first();

        // Limpeza (apenas para recriar dados limpos)
        DB::table('resource_attribute_values')->where('resource_type', $morphType)->delete();
        DB::table('inspections')->where('inspectable_type', $morphType)->delete();
        DB::table('assistive_technology_deficiency')->delete();
        DB::table('assistive_technologies')->delete();

        // --- 1. Cadeira de Rodas Manual ---
        $cadeiraId = DB::table('assistive_technologies')->insertGetId([
            'name' => 'Cadeira de Rodas Manual Padrão',
            'description' => 'Cadeira de rodas manual dobrável, estrutura em aço carbono, assento estofado e rodas traseiras com aro de propulsão. Ideal para uso diário, proporcionando mobilidade e independência para pessoas com dificuldade de locomoção.',
            'type_id' => $types['Cadeira de Rodas'] ?? null,
            'status_id' => $statuses['available'] ?? null,
            'asset_code' => 'CR-2025-001',
            'quantity' => 5,
            'quantity_available' => 5,
            'conservation_state' => ConservationState::GOOD->value,
            'requires_training' => false,
            'notes' => 'Cadeira dobrável, fácil de transportar. Recomenda-se verificar a calibragem dos pneus periodicamente.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Atributos da cadeira
        $cadeiraAttrs = [
            'marca' => 'Freedom',
            'modelo' => 'F-2000',
            'cor' => 'Preta',
            'material' => 'Aço carbono',
            'dimensoes_cm' => '100x60x90',
            'peso_kg' => '15.5',
            'capacidade_kg' => '120',
            'numero_serie' => 'CR-001-2025',
            'ano_fabricacao' => '2024',
        ];
        $this->insertAttributes($cadeiraId, $morphType, $cadeiraAttrs, $attributes, $now);

        // Deficiências atendidas
        $this->attachDeficiencies($cadeiraId, ['Física'], $deficiencias, $now);

        // Inspeções da cadeira
        $this->createInspections($cadeiraId, $morphType, $userId, [
            [
                'type' => InspectionType::INITIAL,
                'state' => ConservationState::NEW,
                'date' => '2024-01-15',
                'description' => 'Vistoria inicial: cadeira nova, lacrada, sem avarias. Todos os componentes funcionando perfeitamente.'
            ],
            [
                'type' => InspectionType::RETURN,
                'state' => ConservationState::GOOD,
                'date' => '2024-03-20',
                'description' => 'Retorno de empréstimo: cadeira apresentava leves sinais de uso, pneus com pequeno desgaste, mas ainda em bom estado. Realizada limpeza e lubrificação.'
            ],
            [
                'type' => InspectionType::MAINTENANCE,
                'state' => ConservationState::REGULAR,
                'date' => '2024-06-10',
                'description' => 'Manutenção preventiva: identificado desgaste nos rolamentos das rodas dianteiras. Substituídos e ajustados. Cadeira agora está regular.'
            ],
            [
                'type' => InspectionType::PERIODIC,
                'state' => ConservationState::GOOD,
                'date' => '2024-09-05',
                'description' => 'Vistoria periódica: cadeira em bom estado, apenas pequenos arranhões na pintura. Recomenda-se nova manutenção em 3 meses.'
            ],
        ]);

        // --- 2. Bengala Longa Dobrável ---
        $bengalaId = DB::table('assistive_technologies')->insertGetId([
            'name' => 'Bengala Longa Dobrável em Alumínio',
            'description' => 'Bengala longa dobrável fabricada em alumínio leve, com ponteira de metal resistente ao desgaste. Utilizada por pessoas com deficiência visual para orientação e mobilidade, permitindo a detecção de obstáculos no solo.',
            'type_id' => $types['Bengala'] ?? null,
            'status_id' => $statuses['available'] ?? null,
            'asset_code' => 'BEN-2025-002',
            'quantity' => 10,
            'quantity_available' => 10,
            'conservation_state' => ConservationState::NEW->value,
            'requires_training' => true,
            'notes' => 'Bengala com ponteira de metal, ideal para orientação. Necessário treinamento para uso adequado.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $bengalaAttrs = [
            'marca' => 'Orion',
            'modelo' => 'BL-300',
            'cor' => 'Branca',
            'material' => 'Alumínio',
            'dimensoes_cm' => '135 (dobrável para 30)',
            'peso_kg' => '0.3',
            'numero_serie' => 'BL-300-001',
            'ano_fabricacao' => '2025',
            'tipo_ponteira' => 'Metal',
        ];
        $this->insertAttributes($bengalaId, $morphType, $bengalaAttrs, $attributes, $now);
        $this->attachDeficiencies($bengalaId, ['Visual'], $deficiencias, $now);

        $this->createInspections($bengalaId, $morphType, $userId, [
            [
                'type' => InspectionType::INITIAL,
                'state' => ConservationState::NEW,
                'date' => '2025-02-01',
                'description' => 'Vistoria inicial: bengala nova, sem defeitos. Ponteira de metal firmemente fixada.'
            ],
            [
                'type' => InspectionType::RETURN,
                'state' => ConservationState::GOOD,
                'date' => '2025-03-15',
                'description' => 'Retorno de empréstimo: bengala em bom estado, apenas pequenos riscos na superfície. Ponteira com leve desgaste, ainda utilizável.'
            ],
        ]);

        // --- 3. Andador com Rodas ---
        $andadorId = DB::table('assistive_technologies')->insertGetId([
            'name' => 'Andador com Rodas 4 Apoios',
            'description' => 'Andador dobrável com quatro rodas, equipado com freios nas duas rodas traseiras e assento de descanso. Proporciona estabilidade e segurança para pessoas com mobilidade reduzida, permitindo caminhadas mais longas.',
            'type_id' => $types['Andador com Rodas'] ?? null,
            'status_id' => $statuses['available'] ?? null,
            'asset_code' => 'AND-2025-003',
            'quantity' => 3,
            'quantity_available' => 3,
            'conservation_state' => ConservationState::GOOD->value,
            'requires_training' => false,
            'notes' => 'Andador dobrável, com freios. Verificar aperto dos parafusos periodicamente.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $andadorAttrs = [
            'marca' => 'OrthoLife',
            'modelo' => 'Walker Pro',
            'cor' => 'Prata',
            'material' => 'Alumínio',
            'dimensoes_cm' => '60x50x80',
            'peso_kg' => '3.2',
            'capacidade_kg' => '120',
            'altura_regulavel_cm' => '75-95',
            'numero_serie' => 'WALK-001',
            'ano_fabricacao' => '2024',
        ];
        $this->insertAttributes($andadorId, $morphType, $andadorAttrs, $attributes, $now);
        $this->attachDeficiencies($andadorId, ['Física'], $deficiencias, $now);

        $this->createInspections($andadorId, $morphType, $userId, [
            [
                'type' => InspectionType::INITIAL,
                'state' => ConservationState::NEW,
                'date' => '2024-05-10',
                'description' => 'Vistoria inicial: andador novo, embalagem original. Freios funcionando perfeitamente.'
            ],
            [
                'type' => InspectionType::RETURN,
                'state' => ConservationState::GOOD,
                'date' => '2024-07-22',
                'description' => 'Retorno de empréstimo: andador com pequenos arranhões, mas estruturalmente íntegro. Freios revisados e ajustados.'
            ],
            [
                'type' => InspectionType::MAINTENANCE,
                'state' => ConservationState::REGULAR,
                'date' => '2024-10-05',
                'description' => 'Manutenção corretiva: uma das rodas estava com ruído. Realizada lubrificação e substituição do rolamento. Agora está regular.'
            ],
        ]);

        // --- 4. Órtese de Punho ---
        $orteseId = DB::table('assistive_technologies')->insertGetId([
            'name' => 'Órtese de Punho Imobilizadora',
            'description' => 'Órtese de punho em neoprene com tala removível, indicada para imobilização e suporte em casos de lesões como tendinite, LER ou pós-operatório. Proporciona estabilidade e alívio da dor.',
            'type_id' => $types['Órtese de Punho'] ?? null,
            'status_id' => $statuses['available'] ?? null,
            'asset_code' => 'ORT-2025-004',
            'quantity' => 6,
            'quantity_available' => 6,
            'conservation_state' => ConservationState::NEW->value,
            'requires_training' => false,
            'notes' => 'Imobilizador para punho, tamanho único. Pode ser lavado.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $orteseAttrs = [
            'marca' => 'Medi',
            'modelo' => 'PunhoFix',
            'material' => 'Neoprene',
            'dimensoes_cm' => '20x10x5',
            'peso_kg' => '0.2',
            'numero_serie' => 'PF-001',
            'ano_fabricacao' => '2025',
            'parte_corpo' => 'Punho',
            'lado' => 'Ambos',
            'tamanho' => 'Único',
        ];
        $this->insertAttributes($orteseId, $morphType, $orteseAttrs, $attributes, $now);
        $this->attachDeficiencies($orteseId, ['Física'], $deficiencias, $now);

        $this->createInspections($orteseId, $morphType, $userId, [
            [
                'type' => InspectionType::INITIAL,
                'state' => ConservationState::NEW,
                'date' => '2025-03-01',
                'description' => 'Vistoria inicial: órtese nova, embalagem lacrada. Material em perfeito estado.'
            ],
        ]);

        // --- 5. Leitor de Tela NVDA (software) ---
        $softwareId = DB::table('assistive_technologies')->insertGetId([
            'name' => 'NVDA (NonVisual Desktop Access)',
            'description' => 'Leitor de tela livre e de código aberto para Windows. Permite que pessoas com deficiência visual utilizem o computador através de síntese de voz e braille. Suporta diversos aplicativos e navegadores.',
            'type_id' => $types['Leitor de Tela'] ?? null,
            'status_id' => $statuses['available'] ?? null,
            'asset_code' => 'SW-2025-005',
            'quantity' => 1,
            'quantity_available' => 1,
            'conservation_state' => ConservationState::NOT_APPLICABLE->value,
            'requires_training' => true,
            'notes' => 'Licença gratuita, versão 2025.1. Acompanha manual de uso.',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $softwareAttrs = [
            'nome_software' => 'NVDA',
            'versao' => '2025.1',
            'sistema_operacional' => 'Windows 10/11',
            'licenca' => 'GPL v2',
            'idioma' => 'Português',
            'tamanho_arquivo_mb' => '150',
            'descricao' => 'Leitor de tela livre para cegos',
        ];
        $this->insertAttributes($softwareId, $morphType, $softwareAttrs, $attributes, $now);
        $this->attachDeficiencies($softwareId, ['Visual'], $deficiencias, $now);

        $this->createInspections($softwareId, $morphType, $userId, [
            [
                'type' => InspectionType::INITIAL,
                'state' => ConservationState::NOT_APPLICABLE,
                'date' => '2025-04-10',
                'description' => 'Vistoria inicial: software instalado e testado. Compatibilidade verificada com Windows 11.'
            ],
            [
                'type' => InspectionType::MAINTENANCE,
                'state' => ConservationState::NOT_APPLICABLE,
                'date' => '2025-06-15',
                'description' => 'Atualização de versão: software atualizado para 2025.2. Testes de funcionalidade realizados.'
            ],
        ]);

        $this->command->info('Assistive technologies seeded: 5 records with multiple inspections.');
    }

    private function insertAttributes($resourceId, $morphType, array $attrs, $attributesMap, $now)
    {
        foreach ($attrs as $name => $value) {
            $key = strtolower($name);
            if ($attributesMap->has($key)) {
                DB::table('resource_attribute_values')->insert([
                    'resource_id' => $resourceId,
                    'resource_type' => $morphType,
                    'attribute_id' => $attributesMap->get($key)->id,
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function attachDeficiencies($resourceId, array $defNames, $deficiencias, $now)
    {
        foreach ($defNames as $defName) {
            if (isset($deficiencias[$defName])) {
                DB::table('assistive_technology_deficiency')->insert([
                    'assistive_technology_id' => $resourceId,
                    'deficiency_id' => $deficiencias[$defName],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    private function createInspections($resourceId, $morphType, $userId, array $inspections)
    {
        foreach ($inspections as $inspection) {
            DB::table('inspections')->insert([
                'inspectable_id' => $resourceId,
                'inspectable_type' => $morphType,
                'state' => $inspection['state']->value,
                'status' => null,
                'type' => $inspection['type']->value,
                'inspection_date' => $inspection['date'],
                'description' => $inspection['description'],
                'user_id' => $userId,
                'created_at' => $inspection['date'] . ' 00:00:00',
                'updated_at' => $inspection['date'] . ' 00:00:00',
            ]);
        }
    }
}
