<?php

namespace Database\Seeders\InclusiveRadar;

use App\Models\InclusiveRadar\InstitutionalEvent;
use Illuminate\Database\Seeder;

class InstitutionalEventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            [
                'title'       => 'Semana da Educação Inclusiva',
                'description' => 'Semana dedicada a atividades e palestras sobre práticas de educação inclusiva.',
                'start_date'  => now()->addDays(1)->format('Y-m-d'),
                'end_date'    => now()->addDays(5)->format('Y-m-d'),
                'start_time'  => '08:00',
                'end_time'    => '17:00',
                'location'    => 'Auditório Principal',
                'organizer'   => 'Coordenação Pedagógica',
                'audience'    => 'Professores e Gestores',
                'is_active'   => true,
            ],
            [
                'title'       => 'Oficina de Tecnologias Assistivas',
                'description' => 'Capacitação prática sobre o uso de tecnologias assistivas em sala de aula.',
                'start_date'  => now()->addDays(7)->format('Y-m-d'),
                'end_date'    => now()->addDays(7)->format('Y-m-d'),
                'start_time'  => '09:00',
                'end_time'    => '12:00',
                'location'    => 'Laboratório de Informática',
                'organizer'   => 'Setor de Apoio Especializado',
                'audience'    => 'Professores',
                'is_active'   => true,
            ],
            [
                'title'       => 'Palestra: Transtorno do Espectro Autista na Escola',
                'description' => 'Palestra com especialistas sobre estratégias de inclusão para alunos com TEA.',
                'start_date'  => now()->addDays(10)->format('Y-m-d'),
                'end_date'    => now()->addDays(10)->format('Y-m-d'),
                'start_time'  => '14:00',
                'end_time'    => '16:00',
                'location'    => 'Sala de Reuniões',
                'organizer'   => 'Equipe Multidisciplinar',
                'audience'    => 'Toda a Comunidade Escolar',
                'is_active'   => true,
            ],
            [
                'title'       => 'Formação Continuada em Libras',
                'description' => 'Módulo introdutório de Língua Brasileira de Sinais para profissionais da educação.',
                'start_date'  => now()->addDays(14)->format('Y-m-d'),
                'end_date'    => now()->addDays(16)->format('Y-m-d'),
                'start_time'  => '13:00',
                'end_time'    => '17:00',
                'location'    => 'Sala 102',
                'organizer'   => 'Intérprete Educacional',
                'audience'    => 'Professores e Funcionários',
                'is_active'   => true,
            ],
            [
                'title'       => 'Encontro de Famílias e Escola',
                'description' => 'Reunião para aproximar famílias de alunos com necessidades especiais e a equipe escolar.',
                'start_date'  => now()->addDays(20)->format('Y-m-d'),
                'end_date'    => now()->addDays(20)->format('Y-m-d'),
                'start_time'  => '18:00',
                'end_time'    => '20:00',
                'location'    => 'Ginásio Escolar',
                'organizer'   => 'Direção Escolar',
                'audience'    => 'Famílias e Responsáveis',
                'is_active'   => true,
            ],
        ];

        foreach ($events as $event) {
            InstitutionalEvent::create($event);
        }
    }
}
