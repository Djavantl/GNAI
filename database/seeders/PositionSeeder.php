<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('positions')->insert([
            [
                'name' => 'Professor AEE',
                'description' => 'Responsável pelo Atendimento Educacional Especializado e elaboração de planos de intervenção pedagógica.'
            ],
            [
                'name' => 'Coordenador do NAPNE',
                'description' => 'Profissional responsável pela gestão das ações do núcleo, articulação institucional e acompanhamento dos atendimentos.'
            ],
            [
                'name' => 'Psicólogo',
                'description' => 'Atua no acompanhamento psicossocial, avaliação e apoio aos estudantes e suas famílias.'
            ],
            [
                'name' => 'Assistente Social',
                'description' => 'Responsável por ações de apoio sociofamiliar, mediação institucional e encaminhamentos externos.'
            ],
            [
                'name' => 'Fonoaudiólogo',
                'description' => 'Atua no desenvolvimento da comunicação, linguagem e intervenção em dificuldades comunicativas.'
            ],
            [
                'name' => 'Intérprete de Libras',
                'description' => 'Realiza a mediação comunicativa entre pessoas surdas e ouvintes por meio da Língua Brasileira de Sinais.'
            ],
            [
                'name' => 'Terapeuta Ocupacional',
                'description' => 'Desenvolve intervenções voltadas à funcionalidade, autonomia e acessibilidade do estudante.'
            ],
        ]);
    }
}
