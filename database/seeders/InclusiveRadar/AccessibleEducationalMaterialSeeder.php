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

        // 🔑 Descobre o morph type correto (MorphMap-safe)
        $modelClass = get_class($model);
        $morphType = array_search($modelClass, Relation::morphMap(), true) ?: $modelClass;

        // Mapas auxiliares
        $typesMap = DB::table('resource_types')->pluck('id', 'name');
        $statusId = DB::table('resource_statuses')->where('is_active', true)->value('id');
        $userId = DB::table('users')->first()?->id;

        // Atributos normalizados (lowercase)
        $attributesMap = DB::table('type_attributes')
            ->get(['id', 'name'])
            ->keyBy(fn ($item) => strtolower($item->name));

        $deficienciesMap = DB::table('deficiencies')->pluck('id', 'name');
        $featuresMap = DB::table('accessibility_features')->pluck('id', 'name');

        // 🧹 Limpeza segura
        DB::table('resource_attribute_values')->where('resource_type', $morphType)->delete();
        DB::table('inspections')->where('inspectable_type', $morphType)->delete();
        DB::table('accessible_educational_material_accessibility')->delete();
        DB::table('accessible_educational_material_deficiency')->delete();
        DB::table('accessible_educational_materials')->delete();

        // 1️⃣ Registro principal
        $materialId = DB::table('accessible_educational_materials')->insertGetId([
            'name' => 'Livro de Matemática em Braille',
            'type_id' => $typesMap['Livro'] ?? null,
            'status_id' => $statusId,
            'asset_code' => 'MPA-001',
            'quantity' => 5,
            'quantity_available' => 5,
            'requires_training' => false,
            'is_active' => true,
            'notes' => 'Carga inicial via seeder.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2️⃣ Inspeção inicial (igual AssistiveTechnology)
        DB::table('inspections')->insert([
            'inspectable_id'   => $materialId,
            'inspectable_type' => $morphType,
            'state'            => ConservationState::NEW->value,
            'type'             => InspectionType::INITIAL->value,
            'inspection_date'  => $now->format('Y-m-d'),
            'description'      => 'Carga inicial via seeder.',
            'user_id'          => $userId,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        // 3️⃣ Atributos dinâmicos
        $specs = [
            'pages'           => '120',
            'discipline'      => 'Matemática',
            'content_summary' => 'Resumo do conteúdo para teste.'
        ];

        foreach ($specs as $name => $val) {
            $key = strtolower($name);

            if ($attributesMap->has($key)) {
                DB::table('resource_attribute_values')->insert([
                    'resource_id'   => $materialId,
                    'resource_type' => $morphType,
                    'attribute_id'  => $attributesMap->get($key)->id,
                    'value'         => $val,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }

        // 4️⃣ Recursos de acessibilidade
        foreach (['Braille', 'Audiodescrição', 'Fonte Ampliada'] as $name) {
            if (isset($featuresMap[$name])) {
                DB::table('accessible_educational_material_accessibility')->insert([
                    'accessible_educational_material_id' => $materialId,
                    'accessibility_feature_id' => $featuresMap[$name],
                ]);
            }
        }

        // 5️⃣ Deficiências atendidas
        foreach (['Visual', 'Auditiva'] as $name) {
            if (isset($deficienciesMap[$name])) {
                DB::table('accessible_educational_material_deficiency')->insert([
                    'accessible_educational_material_id' => $materialId,
                    'deficiency_id' => $deficienciesMap[$name],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }
}
