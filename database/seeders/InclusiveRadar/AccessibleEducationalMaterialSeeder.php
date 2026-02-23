<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccessibleEducationalMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $statusId = DB::table('resource_statuses')->first()->id ?? 1;
        $deficiencies = DB::table('deficiencies')->pluck('id', 'name');
        $features = DB::table('accessibility_features')->pluck('id', 'name');

        // 1. Livro em Braille
        $typeLivro = DB::table('resource_types')->where('name', 'Livro Impresso')->first();
        $mpa1Id = DB::table('accessible_educational_materials')->insertGetId([
            'name' => 'Dom Casmurro - Versão Braille',
            'type_id' => $typeLivro->id,
            'asset_code' => 'MPA-LIV-001',
            'quantity' => 3,
            'quantity_available' => 3,
            'conservation_state' => 'bom',
            'status_id' => $statusId,
            'created_at' => $now,
        ]);

        $this->seedAttributes($mpa1Id, 'accessible_educational_material', [
            'autor' => 'Machado de Assis',
            'disciplina' => 'Literatura',
            'idioma' => 'Português'
        ]);

        // 2. PDF Acessível
        $typePdf = DB::table('resource_types')->where('name', 'PDF Acessível')->first();
        $mpa2Id = DB::table('accessible_educational_materials')->insertGetId([
            'name' => 'Apostila de Cálculo I Acessível',
            'type_id' => $typePdf->id,
            'asset_code' => 'MPA-DIG-001',
            'quantity' => 1,
            'quantity_available' => 1,
            'status_id' => $statusId,
            'created_at' => $now,
        ]);

        // Vínculos de Acessibilidade e Deficiência
        if (isset($features['Braille'])) {
            DB::table('accessible_educational_material_accessibility')->insert([
                'accessible_educational_material_id' => $mpa1Id,
                'accessibility_feature_id' => $features['Braille']
            ]);
        }

        DB::table('accessible_educational_material_deficiency')->insert([
            ['accessible_educational_material_id' => $mpa1Id, 'deficiency_id' => $deficiencies['Visual'] ?? 2],
            ['accessible_educational_material_id' => $mpa2Id, 'deficiency_id' => $deficiencies['Visual'] ?? 2],
        ]);
    }

    private function seedAttributes($resourceId, $type, $values)
    {
        foreach ($values as $attrName => $val) {
            $attr = DB::table('type_attributes')->where('name', $attrName)->first();
            if ($attr) {
                DB::table('resource_attribute_values')->insert([
                    'resource_id' => $resourceId,
                    'resource_type' => $type,
                    'attribute_id' => $attr->id,
                    'value' => $val,
                    'created_at' => now()
                ]);
            }
        }
    }
}