<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AccessibilityFeatureSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $features = [
            [
                'name' => 'Braille',
                'description' => 'Material disponível em sistema Braille para pessoas com deficiência visual.',
            ],
            [
                'name' => 'Audiodescrição',
                'description' => 'Recurso de audiodescrição para compreensão de conteúdos visuais.',
            ],
            [
                'name' => 'Fonte Ampliada',
                'description' => 'Material com fonte ampliada para facilitar a leitura.',
            ],
            [
                'name' => 'Alto Contraste',
                'description' => 'Uso de cores em alto contraste para melhor visualização.',
            ],
            [
                'name' => 'Libras',
                'description' => 'Material com tradução ou interpretação em Libras.',
            ],
            [
                'name' => 'Legenda',
                'description' => 'Conteúdo com legendas para apoio à compreensão.',
            ],
            [
                'name' => 'Texto Simples',
                'description' => 'Conteúdo adaptado com linguagem simples e objetiva.',
            ],
            [
                'name' => 'Áudio',
                'description' => 'Material disponibilizado em formato de áudio.',
            ],
            [
                'name' => 'Digital Acessível',
                'description' => 'Arquivo digital compatível com leitores de tela.',
            ],
        ];

        foreach ($features as $feature) {
            DB::table('accessibility_features')->updateOrInsert(
                ['name' => $feature['name']],
                array_merge($feature, [
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])
            );
        }
    }
}
