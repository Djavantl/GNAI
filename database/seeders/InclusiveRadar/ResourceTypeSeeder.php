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
            // Materiais educacionais não digitais
            ['name' => 'Livro Impresso', 'for_educational_material' => true, 'is_digital' => false],
            ['name' => 'Apostila', 'for_educational_material' => true, 'is_digital' => false],
            ['name' => 'Mapa Tátil', 'for_educational_material' => true, 'is_digital' => false],
            ['name' => 'Maquete', 'for_educational_material' => true, 'is_digital' => false],
            ['name' => 'Jogo Educativo', 'for_educational_material' => true, 'is_digital' => false],
            ['name' => 'Cartaz', 'for_educational_material' => true, 'is_digital' => false],
            ['name' => 'Ábaco', 'for_educational_material' => true, 'is_digital' => false],
            ['name' => 'Material Dourado', 'for_educational_material' => true, 'is_digital' => false],
            ['name' => 'Quebra-cabeça Tátil', 'for_educational_material' => true, 'is_digital' => false],

            // Materiais educacionais digitais
            ['name' => 'PDF Acessível', 'for_educational_material' => true, 'is_digital' => true],
            ['name' => 'EPUB', 'for_educational_material' => true, 'is_digital' => true],
            ['name' => 'Apresentação (PowerPoint)', 'for_educational_material' => true, 'is_digital' => true],
            ['name' => 'Vídeo Educacional', 'for_educational_material' => true, 'is_digital' => true],
            ['name' => 'Áudio Educacional', 'for_educational_material' => true, 'is_digital' => true],
            ['name' => 'Infográfico Interativo', 'for_educational_material' => true, 'is_digital' => true],
            ['name' => 'Simulador', 'for_educational_material' => true, 'is_digital' => true],
            ['name' => 'Objeto de Aprendizagem', 'for_educational_material' => true, 'is_digital' => true],

            // Tecnologias assistivas não digitais
            ['name' => 'Cadeira de Rodas', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Andador', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Muleta', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Bengala', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Bengala Longa', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Andador com Rodas', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Prótese de Membros', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Órtese de Punho', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Órtese de Tronco', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Teclado Adaptado', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Mouse Adaptado', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Ponteira de Cabeça', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Acionador de Pressão', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Comunicador de Prancha', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Lupa Manual', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Lupa Eletrônica', 'for_assistive_technology' => true, 'is_digital' => true],
            ['name' => 'Reglete', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Punção', 'for_assistive_technology' => true, 'is_digital' => false],
            ['name' => 'Máquina Braille', 'for_assistive_technology' => true, 'is_digital' => false],

            // Tecnologias assistivas digitais (softwares)
            ['name' => 'Leitor de Tela', 'for_assistive_technology' => true, 'is_digital' => true],
            ['name' => 'Ampliador de Tela', 'for_assistive_technology' => true, 'is_digital' => true],
            ['name' => 'Software de Comunicação Alternativa', 'for_assistive_technology' => true, 'is_digital' => true],
            ['name' => 'Dosvox', 'for_assistive_technology' => true, 'is_digital' => true],
            ['name' => 'NVDA', 'for_assistive_technology' => true, 'is_digital' => true],
            ['name' => 'Virtual Vision', 'for_assistive_technology' => true, 'is_digital' => true],
            ['name' => 'Teclado Virtual', 'for_assistive_technology' => true, 'is_digital' => true],
            ['name' => 'Reconhecedor de Voz', 'for_assistive_technology' => true, 'is_digital' => true],
        ];

        foreach ($types as $type) {
            DB::table('resource_types')->updateOrInsert(
                ['name' => $type['name']],
                [
                    'for_assistive_technology' => $type['for_assistive_technology'] ?? false,
                    'for_educational_material'  => $type['for_educational_material'] ?? false,
                    'is_digital'                => $type['is_digital'] ?? false,
                    'is_active'                  => true,
                    'created_at'                 => $now,
                    'updated_at'                 => $now,
                ]
            );
        }

        $this->command->info('Resource types seeded: ' . count($types) . ' records.');
    }
}
