<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SpecializedEducationalSupport\Semester;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Garante que não exista semestre atual duplicado
        Semester::query()->update(['is_current' => false]);

        Semester::insert([
            [
                'year' => 2025,
                'term' => 1,
                'label' => '2025/1',
                'start_date' => '2025-02-01',
                'end_date' => '2025-06-30',
                'is_current' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year' => 2025,
                'term' => 2,
                'label' => '2025/2',
                'start_date' => '2025-08-01',
                'end_date' => '2025-12-15',
                'is_current' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year' => 2026,
                'term' => 1,
                'label' => '2026/1',
                'start_date' => '2026-02-01',
                'end_date' => '2026-06-30',
                'is_current' => true, 
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
