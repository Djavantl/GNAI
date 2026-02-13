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

            [
                'name' => 'Imagens Táteis',
                'description' => 'Imagens em relevo que podem ser exploradas pelo tato.',
            ],
            [
                'name' => 'Maquete Tátil',
                'description' => 'Representação tridimensional tátil de objetos ou espaços.',
            ],
            [
                'name' => 'Comunicação Alternativa',
                'description' => 'Pranchas de comunicação com símbolos e pictogramas.',
            ],
            [
                'name' => 'Material em Relevo',
                'description' => 'Texturas e relevos para percepção tátil.',
            ],
            [
                'name' => 'Espaçamento Ampliado',
                'description' => 'Entrelinhas e margens ampliadas para facilitar a leitura.',
            ],
            [
                'name' => 'Fonte Sem Serifa',
                'description' => 'Utilização de fontes sem serifa para melhor legibilidade.',
            ],
            [
                'name' => 'Capa Adaptada',
                'description' => 'Capa com textura ou formato que facilita o manuseio.',
            ],
            [
                'name' => 'Textura Diferenciada',
                'description' => 'Diferentes texturas para identificação tátil de partes do material.',
            ],
            [
                'name' => 'Letra Bastão',
                'description' => 'Texto em letra bastão (caixa alta) para facilitar a leitura.',
            ],
            [
                'name' => 'QR Code com Áudio',
                'description' => 'Código QR que direciona para versão em áudio do conteúdo.',
            ],
            [
                'name' => 'Vídeo com Janela de Libras',
                'description' => 'Vídeo contendo janela com interpretação em Libras.',
            ],
            [
                'name' => 'Descrição de Imagens',
                'description' => 'Texto alternativo descrevendo imagens para leitores de tela.',
            ],
            [
                'name' => 'Transcrição em Texto',
                'description' => 'Transcrição textual de conteúdos em áudio ou vídeo.',
            ],
            [
                'name' => 'Contraste de Cores',
                'description' => 'Combinação de cores com alto contraste para deficientes visuais.',
            ],
            [
                'name' => 'Versão em Áudio',
                'description' => 'Disponibilização de versão completa em áudio.',
            ],
            [
                'name' => 'Versão em Braille',
                'description' => 'Disponibilização de versão completa em Braille.',
            ],
            [
                'name' => 'Versão em Libras',
                'description' => 'Disponibilização de versão completa em vídeo com Libras.',
            ],
            [
                'name' => 'PDF Acessível',
                'description' => 'Arquivo PDF com tags, ordem de leitura e texto pesquisável.',
            ],
            [
                'name' => 'Navegação por Teclado',
                'description' => 'Material digital navegável apenas com o teclado.',
            ],
            [
                'name' => 'Resumo em Áudio',
                'description' => 'Resumo do conteúdo disponível em formato de áudio.',
            ],
            [
                'name' => 'Pictogramas',
                'description' => 'Uso de símbolos pictográficos para comunicação alternativa.',
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
