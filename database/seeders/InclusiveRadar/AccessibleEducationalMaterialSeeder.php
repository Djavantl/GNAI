<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\InspectionType;

class AccessibleEducationalMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $model = new AccessibleEducationalMaterial();
        $modelClass = get_class($model);
        $morphType = array_search($modelClass, Relation::morphMap(), true) ?: $modelClass;

        // Mapeamentos
        $types = DB::table('resource_types')->where('for_educational_material', true)->pluck('id', 'name');
        $statuses = DB::table('resource_statuses')->where('for_educational_material', true)->pluck('id', 'code');
        $deficiencias = DB::table('deficiencies')->pluck('id', 'name');
        $features = DB::table('accessibility_features')->pluck('id', 'name');
        $attributes = DB::table('type_attributes')->get(['id', 'name'])->keyBy(fn($a) => strtolower($a->name));
        $users = DB::table('users')->pluck('id');
        $userId = $users->first();

        // Limpeza
        DB::table('resource_attribute_values')->where('resource_type', $morphType)->delete();
        DB::table('inspections')->where('inspectable_type', $morphType)->delete();
        DB::table('accessible_educational_material_accessibility')->delete();
        DB::table('accessible_educational_material_deficiency')->delete();
        DB::table('accessible_educational_materials')->delete();

        // --- 1. Livro Impresso em Braille ---
        $livroId = DB::table('accessible_educational_materials')->insertGetId([
            'name' => 'Matemática Básica - Volume 1 (Braille)',
            'type_id' => $types['Livro Impresso'] ?? null,
            'status_id' => $statuses['available'] ?? null,
            'asset_code' => 'MPA-001',
            'quantity' => 3,
            'quantity_available' => 3,
            'conservation_state' => ConservationState::GOOD->value,
            'requires_training' => false,
            'notes' => 'Livro didático de matemática básica transcrito para o sistema Braille. Aborda operações fundamentais, frações e introdução à geometria. Material essencial para alunos com deficiência visual. (Notas adicionais: Livro em Braille, 3 volumes. Capa dura.)',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $livroAttrs = [
            'autor' => 'João da Silva',
            'editora' => 'Editora Acessível',
            'ano_publicacao' => '2023',
            'edicao' => '2ª',
            'isbn' => '978-85-1234-567-8',
            'numero_paginas' => '250',
            'idioma' => 'Português',
            'disciplina' => 'Matemática',
            'conteudo' => 'Operações básicas, frações, geometria',
        ];
        $this->insertAttributes($livroId, $morphType, $livroAttrs, $attributes, $now);
        $this->attachFeatures($livroId, ['Braille', 'Fonte Ampliada'], $features);
        $this->attachDeficiencies($livroId, ['Visual'], $deficiencias, $now);

        $this->createInspections($livroId, $morphType, $userId, [
            [
                'type' => InspectionType::INITIAL,
                'state' => ConservationState::NEW,
                'date' => '2023-02-10',
                'description' => 'Vistoria inicial: livro novo, páginas em Braille nítidas, encadernação perfeita.'
            ],
            [
                'type' => InspectionType::RETURN,
                'state' => ConservationState::GOOD,
                'date' => '2023-05-15',
                'description' => 'Retorno de empréstimo: livro em bom estado, pequenas marcas de uso na capa, mas todas as páginas legíveis.'
            ],
            [
                'type' => InspectionType::PERIODIC,
                'state' => ConservationState::GOOD,
                'date' => '2024-01-20',
                'description' => 'Vistoria periódica: livro ainda em bom estado, recomenda-se cuidado ao manusear para evitar danos às páginas em relevo.'
            ],
        ]);

        // --- 2. PDF Acessível de História ---
        $pdfId = DB::table('accessible_educational_materials')->insertGetId([
            'name' => 'História do Brasil - PDF Acessível',
            'type_id' => $types['PDF Acessível'] ?? null,
            'status_id' => $statuses['available'] ?? null,
            'asset_code' => 'MPA-002',
            'quantity' => 1,
            'quantity_available' => 1,
            'conservation_state' => ConservationState::NOT_APPLICABLE->value,
            'requires_training' => false,
            'notes' => 'Arquivo PDF com marcações estruturais, ordem de leitura e texto pesquisável, totalmente compatível com leitores de tela. Abrange desde o período colonial até a república. (Notas adicionais: Arquivo digital com tags e ordem de leitura.)',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $pdfAttrs = [
            'autor' => 'Maria Oliveira',
            'ano_publicacao' => '2024',
            'numero_paginas' => '180',
            'idioma' => 'Português',
            'formato_arquivo' => 'PDF',
            'tamanho_arquivo_mb' => '12.5',
            'disciplina' => 'História',
            'software_necessario' => 'Adobe Reader, NVDA',
            'conteudo' => 'Período colonial, império, república',
        ];
        $this->insertAttributes($pdfId, $morphType, $pdfAttrs, $attributes, $now);
        $this->attachFeatures($pdfId, ['Digital Acessível', 'Descrição de Imagens', 'Navegação por Teclado'], $features);
        $this->attachDeficiencies($pdfId, ['Visual', 'Física'], $deficiencias, $now);

        $this->createInspections($pdfId, $morphType, $userId, [
            [
                'type' => InspectionType::INITIAL,
                'state' => ConservationState::NOT_APPLICABLE,
                'date' => '2024-03-01',
                'description' => 'Vistoria inicial: arquivo testado com NVDA, leitura correta, imagens com descrição alternativa.'
            ],
            [
                'type' => InspectionType::MAINTENANCE,
                'state' => ConservationState::NOT_APPLICABLE,
                'date' => '2024-06-10',
                'description' => 'Atualização: nova versão do arquivo com correções de marcação. Testado novamente.'
            ],
        ]);

        // --- 3. Vídeo Educacional com Libras ---
        $videoId = DB::table('accessible_educational_materials')->insertGetId([
            'name' => 'Ciências: Sistema Solar (com Libras)',
            'type_id' => $types['Vídeo Educacional'] ?? null,
            'status_id' => $statuses['available'] ?? null,
            'asset_code' => 'MPA-003',
            'quantity' => 1,
            'quantity_available' => 1,
            'conservation_state' => ConservationState::NOT_APPLICABLE->value,
            'requires_training' => false,
            'notes' => 'Vídeo educativo sobre o sistema solar, com janela de interpretação em Libras e legendas descritivas. Produzido com foco na acessibilidade para surdos e ensurdecidos. (Notas adicionais: Vídeo com janela de Libras e legendas.)',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $videoAttrs = [
            'diretor' => 'Canal Educação Inclusiva',
            'produtora' => 'Produtora Acessível',
            'ano_publicacao' => '2024',
            'duracao_minutos' => '25',
            'formato_arquivo' => 'MP4',
            'tamanho_arquivo_mb' => '350',
            'resolucao' => '1920x1080',
            'disciplina' => 'Ciências',
            'descricao' => 'Vídeo sobre o sistema solar com acessibilidade',
        ];
        $this->insertAttributes($videoId, $morphType, $videoAttrs, $attributes, $now);
        $this->attachFeatures($videoId, ['Libras', 'Legenda', 'Audiodescrição'], $features);
        $this->attachDeficiencies($videoId, ['Auditiva', 'Visual'], $deficiencias, $now);

        $this->createInspections($videoId, $morphType, $userId, [
            [
                'type' => InspectionType::INITIAL,
                'state' => ConservationState::NOT_APPLICABLE,
                'date' => '2024-04-15',
                'description' => 'Vistoria inicial: vídeo reproduzido, áudio claro, legendas sincronizadas, janela de Libras visível.'
            ],
        ]);

        // --- 4. Mapa Tátil do Brasil ---
        $mapaId = DB::table('accessible_educational_materials')->insertGetId([
            'name' => 'Mapa Tátil do Brasil',
            'type_id' => $types['Mapa Tátil'] ?? null,
            'status_id' => $statuses['available'] ?? null,
            'asset_code' => 'MPA-004',
            'quantity' => 2,
            'quantity_available' => 2,
            'conservation_state' => ConservationState::NEW->value,
            'requires_training' => true,
            'notes' => 'Mapa em relevo com texturas para cada região, incluindo limites estaduais e principais rios. Desenvolvido para alunos com deficiência visual, permitindo a exploração tátil da geografia nacional. (Notas adicionais: Mapa em relevo com texturas para cada região.)',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $mapaAttrs = [
            'material' => 'Papel cartão e EVA',
            'dimensoes_cm' => '80x60',
            'escala' => '1:5.000.000',
            'descricao' => 'Relevo com divisões estaduais e regiões',
        ];
        $this->insertAttributes($mapaId, $morphType, $mapaAttrs, $attributes, $now);
        $this->attachFeatures($mapaId, ['Imagens Táteis', 'Material em Relevo', 'Textura Diferenciada'], $features);
        $this->attachDeficiencies($mapaId, ['Visual'], $deficiencias, $now);

        $this->createInspections($mapaId, $morphType, $userId, [
            [
                'type' => InspectionType::INITIAL,
                'state' => ConservationState::NEW,
                'date' => '2024-08-20',
                'description' => 'Vistoria inicial: mapa novo, texturas bem definidas, sem rasgos.'
            ],
        ]);

        // --- 5. Jogo Educativo Adaptado (Quebra-cabeça Tátil) ---
        $jogoId = DB::table('accessible_educational_materials')->insertGetId([
            'name' => 'Quebra-cabeça Tátil: Animais',
            'type_id' => $types['Quebra-cabeça Tátil'] ?? null,
            'status_id' => $statuses['available'] ?? null,
            'asset_code' => 'MPA-005',
            'quantity' => 4,
            'quantity_available' => 4,
            'conservation_state' => ConservationState::GOOD->value,
            'requires_training' => false,
            'notes' => 'Quebra-cabeça de madeira com peças grandes e texturizadas representando animais da fazenda. Cada peça possui um relevo específico para facilitar a identificação tátil. Indicado para crianças com deficiência visual ou intelectual. (Notas adicionais: Peças grandes com texturas.)',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $jogoAttrs = [
            'material' => 'Madeira',
            'dimensoes_cm' => '30x30',
            'numero_pecas' => '12',
            'idade_recomendada' => '4+',
            'descricao' => 'Animais da fazenda em relevo',
            'disciplina' => 'Educação Infantil',
        ];
        $this->insertAttributes($jogoId, $morphType, $jogoAttrs, $attributes, $now);
        $this->attachFeatures($jogoId, ['Comunicação Alternativa', 'Imagens Táteis', 'Pictogramas'], $features);
        $this->attachDeficiencies($jogoId, ['Intelectual', 'Física'], $deficiencias, $now);

        $this->createInspections($jogoId, $morphType, $userId, [
            [
                'type' => InspectionType::INITIAL,
                'state' => ConservationState::NEW,
                'date' => '2024-09-05',
                'description' => 'Vistoria inicial: jogo novo, todas as peças presentes, texturas nítidas.'
            ],
            [
                'type' => InspectionType::RETURN,
                'state' => ConservationState::GOOD,
                'date' => '2024-11-10',
                'description' => 'Retorno de empréstimo: uma peça com pequeno desgaste na pintura, mas ainda utilizável. Peças contadas e conferidas.'
            ],
            [
                'type' => InspectionType::MAINTENANCE,
                'state' => ConservationState::REGULAR,
                'date' => '2025-01-15',
                'description' => 'Manutenção: identificada peça com rebarba de madeira. Lixada e tratada. Agora está regular.'
            ],
        ]);

        $this->command->info('Accessible educational materials seeded: 5 records with multiple inspections.');
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

    private function attachFeatures($resourceId, array $featureNames, $features)
    {
        foreach ($featureNames as $name) {
            if (isset($features[$name])) {
                DB::table('accessible_educational_material_accessibility')->insert([
                    'accessible_educational_material_id' => $resourceId,
                    'accessibility_feature_id' => $features[$name],
                ]);
            }
        }
    }

    private function attachDeficiencies($resourceId, array $defNames, $deficiencias, $now)
    {
        foreach ($defNames as $defName) {
            if (isset($deficiencias[$defName])) {
                DB::table('accessible_educational_material_deficiency')->insert([
                    'accessible_educational_material_id' => $resourceId,
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
