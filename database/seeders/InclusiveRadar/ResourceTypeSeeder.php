<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ResourceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $types = [
            ['name' => 'Slide', 'for_educational_material' => true],
            ['name' => 'PDF', 'for_educational_material' => true],
            ['name' => 'Livro', 'for_educational_material' => true],
            ['name' => 'Vídeo Educacional', 'for_educational_material' => true],
            ['name' => 'Bengala', 'for_assistive_technology' => true],
            ['name' => 'Prótese', 'for_assistive_technology' => true],
            ['name' => 'Órtese', 'for_assistive_technology' => true],
        ];

        foreach ($types as $type) {
            DB::table('resource_types')->updateOrInsert(
                ['name' => $type['name']],
                [
                    'for_assistive_technology' => $type['for_assistive_technology'] ?? false,
                    'for_educational_material' => $type['for_educational_material'] ?? false,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }
}
