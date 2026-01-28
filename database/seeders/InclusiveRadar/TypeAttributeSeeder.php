<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TypeAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $attributes = [
            ['name' => 'pages', 'label' => 'Número de páginas', 'field_type' => 'integer'],
            ['name' => 'discipline', 'label' => 'Disciplina', 'field_type' => 'string'],
            ['name' => 'content_summary', 'label' => 'Resumo do conteúdo', 'field_type' => 'text'],
            ['name' => 'file_format', 'label' => 'Formato do arquivo', 'field_type' => 'string'],
            ['name' => 'duration', 'label' => 'Duração (minutos)', 'field_type' => 'integer'],
            ['name' => 'grip_type', 'label' => 'Tipo de pegada', 'field_type' => 'string'],
            ['name' => 'body_part', 'label' => 'Parte do corpo', 'field_type' => 'string'],
            ['name' => 'size', 'label' => 'Tamanho', 'field_type' => 'string'],
            ['name' => 'material', 'label' => 'Material', 'field_type' => 'string'],
        ];

        foreach ($attributes as $attribute) {
            DB::table('type_attributes')->updateOrInsert(
                ['name' => $attribute['name']],
                array_merge($attribute, [
                    'is_required' => false,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
