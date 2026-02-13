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

        // Busca todos os tipos e atributos
        $types = DB::table('resource_types')->pluck('id', 'name');
        $attrs = DB::table('type_attributes')->pluck('id', 'name');

        // Definição das associações: tipo => lista de nomes de atributos
        $assignments = [
            // Materiais educacionais não digitais
            'Livro Impresso'       => ['autor', 'editora', 'ano_publicacao', 'edicao', 'isbn', 'numero_paginas', 'idioma', 'disciplina'],
            'Apostila'             => ['autor', 'ano_publicacao', 'numero_paginas', 'disciplina', 'conteudo'],
            'Mapa Tátil'           => ['material', 'dimensoes_cm', 'escala', 'descricao'],
            'Maquete'              => ['material', 'dimensoes_cm', 'escala', 'descricao'],
            'Jogo Educativo'       => ['material', 'dimensoes_cm', 'idade_recomendada', 'disciplina', 'descricao'],
            'Cartaz'               => ['material', 'dimensoes_cm', 'tema', 'descricao'],
            'Ábaco'                => ['material', 'dimensoes_cm', 'descricao'],
            'Material Dourado'     => ['material', 'dimensoes_cm', 'descricao'],
            'Quebra-cabeça Tátil'  => ['material', 'dimensoes_cm', 'numero_pecas', 'descricao'],

            // Materiais educacionais digitais
            'PDF Acessível'        => ['autor', 'ano_publicacao', 'numero_paginas', 'idioma', 'formato_arquivo', 'tamanho_arquivo_mb', 'disciplina', 'software_necessario'],
            'EPUB'                 => ['autor', 'ano_publicacao', 'numero_paginas', 'idioma', 'formato_arquivo', 'tamanho_arquivo_mb', 'disciplina'],
            'Apresentação (PowerPoint)' => ['autor', 'ano_publicacao', 'numero_paginas', 'idioma', 'formato_arquivo', 'tamanho_arquivo_mb', 'disciplina'],
            'Vídeo Educacional'    => ['diretor', 'produtora', 'ano_publicacao', 'duracao_minutos', 'formato_arquivo', 'tamanho_arquivo_mb', 'resolucao', 'disciplina', 'descricao'],
            'Áudio Educacional'    => ['autor', 'ano_publicacao', 'duracao_minutos', 'formato_arquivo', 'tamanho_arquivo_mb', 'disciplina', 'descricao'],
            'Infográfico Interativo' => ['autor', 'ano_publicacao', 'formato_arquivo', 'tamanho_arquivo_mb', 'disciplina', 'descricao', 'software_necessario'],
            'Simulador'            => ['autor', 'ano_publicacao', 'formato_arquivo', 'tamanho_arquivo_mb', 'disciplina', 'descricao', 'software_necessario', 'sistema_operacional'],
            'Objeto de Aprendizagem' => ['autor', 'ano_publicacao', 'formato_arquivo', 'tamanho_arquivo_mb', 'disciplina', 'descricao', 'software_necessario'],

            // Tecnologias assistivas não digitais
            'Cadeira de Rodas'     => ['marca', 'modelo', 'cor', 'material', 'dimensoes_cm', 'peso_kg', 'capacidade_kg', 'numero_serie', 'ano_fabricacao', 'observacoes'],
            'Andador'              => ['marca', 'modelo', 'cor', 'material', 'dimensoes_cm', 'peso_kg', 'capacidade_kg', 'altura_regulavel_cm', 'numero_serie', 'ano_fabricacao'],
            'Muleta'               => ['marca', 'modelo', 'cor', 'material', 'dimensoes_cm', 'peso_kg', 'capacidade_kg', 'altura_regulavel_cm', 'numero_serie', 'ano_fabricacao', 'lado'],
            'Bengala'              => ['marca', 'modelo', 'cor', 'material', 'dimensoes_cm', 'peso_kg', 'capacidade_kg', 'numero_serie', 'ano_fabricacao', 'tipo_ponteira'],
            'Bengala Longa'        => ['marca', 'modelo', 'cor', 'material', 'dimensoes_cm', 'peso_kg', 'numero_serie', 'ano_fabricacao', 'tipo_ponteira'],
            'Andador com Rodas'    => ['marca', 'modelo', 'cor', 'material', 'dimensoes_cm', 'peso_kg', 'capacidade_kg', 'numero_serie', 'ano_fabricacao'],
            'Prótese de Membros'   => ['marca', 'modelo', 'material', 'dimensoes_cm', 'peso_kg', 'numero_serie', 'ano_fabricacao', 'parte_corpo', 'lado', 'tamanho_calcado'],
            'Órtese de Punho'      => ['marca', 'modelo', 'material', 'dimensoes_cm', 'peso_kg', 'numero_serie', 'ano_fabricacao', 'parte_corpo', 'lado', 'tamanho'],
            'Órtese de Tronco'     => ['marca', 'modelo', 'material', 'dimensoes_cm', 'peso_kg', 'numero_serie', 'ano_fabricacao', 'parte_corpo'],
            'Teclado Adaptado'     => ['marca', 'modelo', 'cor', 'material', 'dimensoes_cm', 'peso_kg', 'numero_serie', 'ano_fabricacao', 'tipo_conexao', 'descricao'],
            'Mouse Adaptado'       => ['marca', 'modelo', 'cor', 'material', 'dimensoes_cm', 'peso_kg', 'numero_serie', 'ano_fabricacao', 'tipo_conexao', 'descricao'],
            'Ponteira de Cabeça'   => ['marca', 'modelo', 'material', 'dimensoes_cm', 'peso_kg', 'numero_serie', 'ano_fabricacao', 'descricao'],
            'Acionador de Pressão' => ['marca', 'modelo', 'material', 'dimensoes_cm', 'peso_kg', 'numero_serie', 'ano_fabricacao', 'tipo_acionamento', 'descricao'],
            'Comunicador de Prancha'=> ['marca', 'modelo', 'material', 'dimensoes_cm', 'peso_kg', 'numero_serie', 'ano_fabricacao', 'descricao'],
            'Lupa Manual'          => ['marca', 'modelo', 'material', 'dimensoes_cm', 'peso_kg', 'numero_serie', 'ano_fabricacao', 'ampliacao'],
            'Lupa Eletrônica'      => ['marca', 'modelo', 'material', 'dimensoes_cm', 'peso_kg', 'numero_serie', 'ano_fabricacao', 'ampliacao', 'resolucao', 'tipo_alimentacao'],
            'Reglete'              => ['marca', 'modelo', 'material', 'dimensoes_cm', 'numero_serie', 'ano_fabricacao', 'descricao'],
            'Punção'               => ['marca', 'modelo', 'material', 'dimensoes_cm', 'numero_serie', 'ano_fabricacao', 'descricao'],
            'Máquina Braille'      => ['marca', 'modelo', 'material', 'dimensoes_cm', 'peso_kg', 'numero_serie', 'ano_fabricacao', 'descricao'],

            // Tecnologias assistivas digitais
            'Leitor de Tela'       => ['nome_software', 'versao', 'sistema_operacional', 'licenca', 'idioma', 'tamanho_arquivo_mb', 'descricao'],
            'Ampliador de Tela'    => ['nome_software', 'versao', 'sistema_operacional', 'licenca', 'idioma', 'tamanho_arquivo_mb', 'descricao'],
            'Software de Comunicação Alternativa' => ['nome_software', 'versao', 'sistema_operacional', 'licenca', 'idioma', 'tamanho_arquivo_mb', 'descricao'],
            'Dosvox'               => ['nome_software', 'versao', 'sistema_operacional', 'licenca', 'tamanho_arquivo_mb', 'descricao'],
            'NVDA'                 => ['nome_software', 'versao', 'sistema_operacional', 'licenca', 'tamanho_arquivo_mb', 'idioma', 'descricao'],
            'Virtual Vision'       => ['nome_software', 'versao', 'sistema_operacional', 'licenca', 'tamanho_arquivo_mb', 'idioma', 'descricao'],
            'Teclado Virtual'      => ['nome_software', 'versao', 'sistema_operacional', 'licenca', 'tamanho_arquivo_mb', 'descricao'],
            'Reconhecedor de Voz'  => ['nome_software', 'versao', 'sistema_operacional', 'licenca', 'tamanho_arquivo_mb', 'idioma', 'descricao'],
        ];

        foreach ($assignments as $typeName => $attrNames) {
            if (!isset($types[$typeName])) {
                $this->command->warn("Tipo '{$typeName}' não encontrado. Ignorando associações.");
                continue;
            }

            $typeId = $types[$typeName];

            foreach ($attrNames as $attrName) {
                if (!isset($attrs[$attrName])) {
                    $this->command->warn("Atributo '{$attrName}' não encontrado. Ignorando para tipo '{$typeName}'.");
                    continue;
                }

                DB::table('type_attribute_assignments')->updateOrInsert(
                    [
                        'type_id'      => $typeId,
                        'attribute_id' => $attrs[$attrName],
                    ],
                    [
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }

        $this->command->info('Type attribute assignments seeded.');
    }
}
