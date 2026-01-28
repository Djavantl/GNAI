<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TypeAttributeAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $types = DB::table('resource_types')->pluck('id', 'name');
        $attributes = DB::table('type_attributes')->pluck('id', 'name');

        $assignments = [
            'Slide' => ['pages', 'discipline', 'content_summary', 'file_format'],
            'PDF' => ['pages', 'discipline', 'file_format'],
            'Livro' => ['pages', 'discipline', 'content_summary'],
            'Vídeo Educacional' => ['duration', 'discipline', 'content_summary'],
            'Bengala' => ['grip_type', 'material', 'size'],
            'Prótese' => ['body_part', 'material', 'size'],
            'Órtese' => ['body_part', 'material', 'size'],
        ];

        foreach ($assignments as $typeName => $attributeNames) {
            foreach ($attributeNames as $attributeName) {
                DB::table('type_attribute_assignments')->updateOrInsert(
                    [
                        'type_id' => $types[$typeName],
                        'attribute_id' => $attributes[$attributeName],
                    ],
                    [
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }
    }
}
