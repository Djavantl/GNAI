<?php

namespace Database\Seeders\Inclusion;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $tags = [
            'Acessibilidade',
            'Inclusão',
            'Eventos',
            'Avisos',
            'Formação',
            'Libras',
            'Editais',
            'Notícias',
        ];

        foreach ($tags as $name) {
            DB::table('tags')->updateOrInsert(
                ['name' => $name],
                [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        $this->command->info('Tags seeded: ' . count($tags) . ' records.');
    }
}
