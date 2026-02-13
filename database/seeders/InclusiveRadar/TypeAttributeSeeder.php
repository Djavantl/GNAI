<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TypeAttributeSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $attributes = [
            // Gerais
            ['name' => 'titulo',          'label' => 'Título',               'field_type' => 'string'],
            ['name' => 'descricao',       'label' => 'Descrição',            'field_type' => 'text'],
            ['name' => 'observacoes',     'label' => 'Observações',          'field_type' => 'text'],

            // Livros / materiais impressos
            ['name' => 'autor',            'label' => 'Autor',                'field_type' => 'string'],
            ['name' => 'editora',          'label' => 'Editora',              'field_type' => 'string'],
            ['name' => 'ano_publicacao',   'label' => 'Ano de publicação',    'field_type' => 'integer'],
            ['name' => 'edicao',           'label' => 'Edição',               'field_type' => 'string'],
            ['name' => 'isbn',             'label' => 'ISBN',                 'field_type' => 'string'],
            ['name' => 'numero_paginas',   'label' => 'Número de páginas',    'field_type' => 'integer'],
            ['name' => 'idioma',           'label' => 'Idioma',               'field_type' => 'string'],

            // Materiais digitais
            ['name' => 'formato_arquivo',  'label' => 'Formato do arquivo',   'field_type' => 'string'],
            ['name' => 'tamanho_arquivo_mb','label' => 'Tamanho (MB)',        'field_type' => 'decimal'],
            ['name' => 'duracao_minutos',  'label' => 'Duração (minutos)',    'field_type' => 'integer'],
            ['name' => 'resolucao',        'label' => 'Resolução',            'field_type' => 'string'],
            ['name' => 'software_necessario','label' => 'Software necessário','field_type' => 'string'],

            // Vídeos / áudios
            ['name' => 'diretor',           'label' => 'Diretor',              'field_type' => 'string'],
            ['name' => 'produtora',         'label' => 'Produtora',            'field_type' => 'string'],

            // Tecnologias assistivas (físicas)
            ['name' => 'marca',             'label' => 'Marca',                'field_type' => 'string'],
            ['name' => 'modelo',            'label' => 'Modelo',               'field_type' => 'string'],
            ['name' => 'cor',               'label' => 'Cor',                  'field_type' => 'string'],
            ['name' => 'material',          'label' => 'Material',             'field_type' => 'string'],
            ['name' => 'dimensoes_cm',      'label' => 'Dimensões (cm)',       'field_type' => 'string'],
            ['name' => 'peso_kg',           'label' => 'Peso (kg)',            'field_type' => 'decimal'],
            ['name' => 'capacidade_kg',     'label' => 'Capacidade (kg)',      'field_type' => 'decimal'],
            ['name' => 'altura_regulavel_cm','label' => 'Altura regulável (cm)','field_type' => 'integer'],
            ['name' => 'numero_serie',      'label' => 'Número de série',      'field_type' => 'string'],
            ['name' => 'ano_fabricacao',    'label' => 'Ano de fabricação',    'field_type' => 'integer'],

            // Específicos de órteses/próteses
            ['name' => 'parte_corpo',       'label' => 'Parte do corpo',       'field_type' => 'string'],
            ['name' => 'lado',              'label' => 'Lado (esquerdo/direito)', 'field_type' => 'string'],
            ['name' => 'tamanho_calcado',   'label' => 'Tamanho (calçado)',    'field_type' => 'integer'],

            // Softwares
            ['name' => 'versao',             'label' => 'Versão',              'field_type' => 'string'],
            ['name' => 'sistema_operacional','label' => 'Sistema operacional', 'field_type' => 'string'],
            ['name' => 'licenca',            'label' => 'Tipo de licença',     'field_type' => 'string'],

            // Educacionais
            ['name' => 'disciplina',         'label' => 'Disciplina',          'field_type' => 'string'],
            ['name' => 'nivel_ensino',       'label' => 'Nível de ensino',     'field_type' => 'string'],
            ['name' => 'conteudo',           'label' => 'Conteúdo programático','field_type' => 'text'],

            // NOVOS ATRIBUTOS (para eliminar os warnings)
            ['name' => 'escala',             'label' => 'Escala',              'field_type' => 'string'],
            ['name' => 'idade_recomendada',  'label' => 'Idade recomendada',   'field_type' => 'string'],
            ['name' => 'tema',               'label' => 'Tema',                'field_type' => 'string'],
            ['name' => 'numero_pecas',       'label' => 'Número de peças',     'field_type' => 'integer'],
            ['name' => 'tipo_ponteira',      'label' => 'Tipo de ponteira',    'field_type' => 'string'],
            ['name' => 'tamanho',            'label' => 'Tamanho',             'field_type' => 'string'],
            ['name' => 'tipo_conexao',       'label' => 'Tipo de conexão',     'field_type' => 'string'],
            ['name' => 'tipo_acionamento',   'label' => 'Tipo de acionamento', 'field_type' => 'string'],
            ['name' => 'ampliacao',          'label' => 'Ampliação',           'field_type' => 'string'],
            ['name' => 'tipo_alimentacao',   'label' => 'Tipo de alimentação', 'field_type' => 'string'],
            ['name' => 'nome_software',      'label' => 'Nome do software',    'field_type' => 'string'],
        ];

        foreach ($attributes as $attribute) {
            DB::table('type_attributes')->updateOrInsert(
                ['name' => $attribute['name']],
                [
                    'label'       => $attribute['label'],
                    'field_type'  => $attribute['field_type'],
                    'is_required' => false,
                    'is_active'   => true,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]
            );
        }

        $this->command->info('Type attributes seeded: ' . count($attributes) . ' records.');
    }
}
