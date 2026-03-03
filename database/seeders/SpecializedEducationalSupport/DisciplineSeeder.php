<?php

namespace Database\Seeders\SpecializedEducationalSupport;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SpecializedEducationalSupport\Course;
use App\Models\SpecializedEducationalSupport\Discipline;

class DisciplineSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Definição das disciplinas por curso
        $data = [
            'Técnico em Informática' => [
                'Algoritmos e Lógica de Programação',
                'Arquitetura de Computadores',
                'Banco de Dados I',
                'Banco de Dados II',
                'Redes de Computadores',
                'Programação Web Front-end',
                'Programação Web Back-end',
                'Desenvolvimento de Sistemas Móveis',
                'Engenharia de Software',
                'Sistemas Operacionais',
                'Segurança da Informação',
                'Interface Homem-Computador',
                'Estrutura de Dados',
                'Manutenção de Computadores',
                'Ética Profissional e Cidadania'
            ],
            'Técnico em Administração' => [
                'Teoria Geral da Administração',
                'Contabilidade Geral',
                'Gestão de Pessoas I',
                'Gestão de Pessoas II',
                'Comportamento Organizacional',
                'Marketing e Vendas',
                'Logística e Cadeia de Suprimentos',
                'Administração Financeira',
                'Direito Empresarial',
                'Gestão da Qualidade',
                'Empreendedorismo',
                'Processos Administrativos',
                'Comunicação Empresarial',
                'Matemática Financeira',
                'Economia e Mercados'
            ]
        ];

        foreach ($data as $courseName => $disciplines) {
            // Busca o curso correspondente
            $course = Course::where('name', $courseName)->first();

            if ($course) {
                foreach ($disciplines as $name) {
                    // Cria ou busca a disciplina
                    $discipline = Discipline::firstOrCreate(
                        ['name' => $name],
                        ['description' => "Conteúdo programático referente a $name.", 'is_active' => true]
                    );

                    // Vincula ao curso na tabela pivô 'course_disciplines'
                    // Usando DB para evitar necessidade de um Model específico para a pivô
                    DB::table('course_disciplines')->updateOrInsert(
                        [
                            'course_id' => $course->id,
                            'discipline_id' => $discipline->id
                        ],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }
    }
}