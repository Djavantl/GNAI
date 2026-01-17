<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ResourceStatusSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $statuses = [
            [
                'code' => 'available',
                'name' => 'Disponível',
                'description' => 'Recurso disponível para uso e empréstimo.',
                'blocks_loan' => false,
                'blocks_access' => false,
                'for_assistive_technology' => true,
                'for_educational_material' => true,
            ],
            [
                'code' => 'in_use',
                'name' => 'Em uso',
                'description' => 'Recurso atualmente em uso.',
                'blocks_loan' => true,
                'blocks_access' => false,
                'for_assistive_technology' => true,
                'for_educational_material' => false,
            ],
            [
                'code' => 'under_maintenance',
                'name' => 'Em manutenção',
                'description' => 'Recurso em manutenção ou reparo.',
                'blocks_loan' => true,
                'blocks_access' => false,
                'for_assistive_technology' => true,
                'for_educational_material' => true,
            ],
            [
                'code' => 'damaged',
                'name' => 'Danificado',
                'description' => 'Recurso danificado e indisponível temporariamente.',
                'blocks_loan' => true,
                'blocks_access' => false,
                'for_assistive_technology' => true,
                'for_educational_material' => true,
            ],
            [
                'code' => 'unavailable',
                'name' => 'Indisponível',
                'description' => 'Recurso indisponível para acesso.',
                'blocks_loan' => true,
                'blocks_access' => true,
                'for_assistive_technology' => true,
                'for_educational_material' => true,
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('resource_statuses')->updateOrInsert(
                ['code' => $status['code']],
                array_merge($status, [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
