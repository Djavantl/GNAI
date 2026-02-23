<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssistiveTechnologySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $statusId = DB::table('resource_statuses')->first()->id ?? 1;
        $deficiencies = DB::table('deficiencies')->pluck('id', 'name');

        // 1. Cadeira de Rodas (Física)
        $typeCadeira = DB::table('resource_types')->where('name', 'Cadeira de Rodas')->first();
        $ta1Id = DB::table('assistive_technologies')->insertGetId([
            'name' => 'Cadeira de Rodas Motorizada X1',
            'description' => 'Cadeira com comando por joystick e bateria recarregável.',
            'type_id' => $typeCadeira->id,
            'asset_code' => 'TA-CAD-001',
            'quantity' => 2,
            'quantity_available' => 2,
            'conservation_state' => 'novo',
            'status_id' => $statusId,
            'created_at' => $now,
        ]);

        // Atributos da Cadeira
        $this->seedAttributes($ta1Id, 'assistive_technology', [
            'marca' => 'Freedom',
            'modelo' => 'X1-Motor',
            'peso_kg' => '45',
            'capacidade_kg' => '120'
        ]);

        // 2. NVDA (Digital)
        $typeNvda = DB::table('resource_types')->where('name', 'NVDA')->first();
        $ta2Id = DB::table('assistive_technologies')->insertGetId([
            'name' => 'Software NVDA 2024.1',
            'description' => 'Leitor de tela gratuito e de código aberto.',
            'type_id' => $typeNvda->id,
            'asset_code' => 'TA-SOFT-001',
            'quantity' => 50,
            'quantity_available' => 50,
            'conservation_state' => 'novo',
            'status_id' => $statusId,
            'created_at' => $now,
        ]);

        $this->seedAttributes($ta2Id, 'assistive_technology', [
            'versao' => '2024.1',
            'sistema_operacional' => 'Windows 10/11',
            'licenca' => 'GPLv2'
        ]);

        // Vínculos com Deficiências
        DB::table('assistive_technology_deficiency')->insert([
            ['assistive_technology_id' => $ta1Id, 'deficiency_id' => $deficiencies['Física'] ?? 1],
            ['assistive_technology_id' => $ta2Id, 'deficiency_id' => $deficiencies['Visual'] ?? 2],
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