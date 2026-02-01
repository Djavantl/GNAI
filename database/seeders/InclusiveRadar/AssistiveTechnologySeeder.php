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

        // DESCUBRA O MORPH TYPE (Igual ao seu ResourceAttributeValueService)
        // Isso garante que se você usar MorphMap ou ClassName, a seeder vai acertar.
        $modelClass = get_class($model);
        $morphType = array_search($modelClass, Relation::morphMap(), true) ?: $modelClass;

        // 1. Mapeamento de Atributos e Tipos
        // Nota: O seu TypeAttributeService usa 'label', mas aqui buscamos por 'name'
        // Verifique se na sua tabela é 'name' ou 'label'. Ajustei para 'name' baseado no seu código anterior.
        $attributesMap = DB::table('type_attributes')
            ->get(['id', 'name'])
            ->keyBy(fn($item) => strtolower($item->name));

        $typesMap = DB::table('resource_types')->pluck('id', 'name');
        $statusId = DB::table('resource_statuses')->where('is_active', true)->first()?->id;
        $userId = DB::table('users')->first()?->id;

        // Limpeza (Ordem correta para não quebrar chaves estrangeiras)
        DB::table('resource_attribute_values')->where('resource_type', $morphType)->delete();
        DB::table('inspections')->where('inspectable_type', $morphType)->delete();
        DB::table('assistive_technology_deficiency')->delete();
        DB::table('assistive_technologies')->delete();

        // 2. Inserção da Tecnologia
        $techId = DB::table('assistive_technologies')->insertGetId([
            'name' => 'Bengala Longa Dobrável',
            'type_id' => $typesMap['Bengala'] ?? null,
            'status_id' => $statusId,
            'asset_code' => 'PAT-001',
            'quantity' => 10,
            'quantity_available' => 10,
            'conservation_state' => ConservationState::NEW->value,
            'requires_training' => false,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Inspeção Inicial
        DB::table('inspections')->insert([
            'inspectable_id'   => $techId,
            'inspectable_type' => $morphType, // Usando o tipo dinâmico
            'state'            => ConservationState::NEW->value,
            'type'             => InspectionType::INITIAL->value,
            'inspection_date'  => $now->format('Y-m-d'),
            'description'      => 'Carga inicial via seeder.',
            'user_id'          => $userId,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        // 4. Especificações Técnicas (Onde estava o erro)
        // Garanta que essas chaves (material, tamanho, etc) existam na tabela type_attributes
        $specs = [
            'material' => 'Alumínio',
            'size'  => '120cm',
            'grip_type' => 'Ergonômica'
        ];

        foreach ($specs as $name => $val) {
            $searchKey = strtolower($name);

            if ($attributesMap->has($searchKey)) {
                DB::table('resource_attribute_values')->insert([
                    'resource_id'   => $techId,
                    'resource_type' => $morphType, // O segredo está aqui
                    'attribute_id'  => $attributesMap->get($searchKey)->id,
                    'value'         => $val,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }
        }

        // 5. Relacionamento com Deficiência
        $defId = DB::table('deficiencies')
            ->where('name', 'like', '%Visual%')
            ->value('id');

        if ($defId) {
            DB::table('assistive_technology_deficiency')->insert([
                'assistive_technology_id' => $techId,
                'deficiency_id' => $defId,
            ]);
        }
    }
}
