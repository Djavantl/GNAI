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
                'description' => 'Descrição em áudio de elementos visuais para pessoas com deficiência visual.',
            ],
            [
                'name' => 'Libras',
                'description' => 'Conteúdo com interpretação ou tradução em Libras.',
            ],
            [
                'name' => 'Legenda',
                'description' => 'Conteúdo audiovisual com legendas para apoio à compreensão.',
            ],
            [
                'name' => 'Áudio',
                'description' => 'Material disponibilizado em formato de áudio.',
            ],
            [
                'name' => 'Digital Acessível',
                'description' => 'Arquivo digital compatível com leitores de tela.',
            ],
            [
                'name' => 'PDF Acessível',
                'description' => 'PDF estruturado com tags e ordem de leitura adequada.',
            ],
            [
                'name' => 'Fonte Ampliada',
                'description' => 'Texto com tamanho ampliado para facilitar a leitura.',
            ],
            [
                'name' => 'Alto Contraste',
                'description' => 'Uso de cores com alto contraste para melhor visualização.',
            ],
            [
                'name' => 'Texto Simples',
                'description' => 'Conteúdo com linguagem simplificada e de fácil compreensão.',
            ],
            [
                'name' => 'Comunicação Alternativa',
                'description' => 'Uso de símbolos, pictogramas ou pranchas de comunicação.',
            ],
            [
                'name' => 'Imagens Táteis',
                'description' => 'Imagens em relevo que podem ser exploradas pelo tato.',
            ],
            [
                'name' => 'Maquete Tátil',
                'description' => 'Representação tridimensional tátil de objetos ou espaços.',
            ],
            [
                'name' => 'Navegação por Teclado',
                'description' => 'Conteúdo digital que pode ser utilizado apenas com o teclado.',
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

        $this->command->info('Accessibility features seeded: ' . count($features) . ' records.');
    }
}
