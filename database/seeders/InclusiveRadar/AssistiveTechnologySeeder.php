<?php

namespace Database\Seeders\InclusiveRadar;

use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\ResourceType;
use App\Models\InclusiveRadar\ResourceStatus;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Enums\InclusiveRadar\ConservationState;
use App\Models\InclusiveRadar\Inspection;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AssistiveTechnologySeeder extends Seeder
{
    public function run(): void
    {
        // Obtém os tipos de tecnologia assistiva (físicas e digitais)
        $types = ResourceType::where('for_assistive_technology', true)->get();

        if ($types->isEmpty()) {
            $this->command->error('Nenhum tipo de tecnologia assistiva encontrado. Execute ResourceTypeSeeder primeiro.');
            return;
        }

        // Obtém o status "Disponível"
        $availableStatus = ResourceStatus::where('code', 'available')->first();
        if (!$availableStatus) {
            $this->command->error('Status "available" não encontrado. Execute ResourceStatusSeeder primeiro.');
            return;
        }

        // Obtém algumas deficiências para associar (opcional)
        $deficiencies = Deficiency::inRandomOrder()->limit(5)->get();

        // Obtém um usuário para associar às inspeções
        $user = User::first();
        if (!$user) {
            $this->command->error('Nenhum usuário encontrado. Crie um usuário antes de executar este seeder.');
            return;
        }

        // Lista de 15 nomes sugestivos
        $technologyNames = [
            'Cadeira de Rodas Motorizada',
            'Andador com Suporte de Tronco',
            'Bengala Longa Dobrável',
            'Lupa Eletrônica de Mesa',
            'Teclado Adaptado com Colmeia',
            'Mouse Adaptado Trackball',
            'Software Leitor de Tela (NVDA)',
            'Software Ampliador de Tela (Virtual Vision)',
            'Comunicador Alternativo Prancha de Símbolos',
            'Punção para Escrita Braille',
            'Reglete de Mesa',
            'Órtese de Punho e Polegar',
            'Prótese Transtibial',
            'Acionador de Pressão por Sopro',
            'Ponteira de Cabeça para Digitação',
        ];

        $namesToUse = array_slice($technologyNames, 0, 15);

        foreach ($namesToUse as $index => $name) {
            $type = $types->random();
            $conservation = ConservationState::cases()[array_rand(ConservationState::cases())];

            $technology = AssistiveTechnology::create([
                'name'                => $name,
                'description'         => "Tecnologia assistiva: {$name}. Recurso disponível para empréstimo.",
                'type_id'             => $type->id,
                'asset_code'          => 'TA-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                'quantity'            => 1,
                'quantity_available'  => 1,
                'conservation_state'  => $conservation->value,
                'notes'               => 'Item cadastrado via seeder com estoque unitário.',
                'status_id'           => $availableStatus->id,
                'is_active'           => true,
            ]);

            if ($deficiencies->isNotEmpty()) {
                $technology->deficiencies()->sync(
                    $deficiencies->random(rand(1, min(3, $deficiencies->count())))->pluck('id')
                );
            }

            // Define um status válido para o enum BarrierStatus (se a model Inspection tiver cast)
            // Opções: 'identified', 'under_analysis', 'in_progress', 'resolved', 'not_applicable'
            $inspectionStatus = 'identified'; // ou 'not_applicable' se preferir

            // Cria a primeira inspeção
            Inspection::create([
                'inspectable_id'   => $technology->id,
                'inspectable_type' => AssistiveTechnology::class,
                'state'            => $conservation->value,
                'status'           => $inspectionStatus,
                'type'             => 'initial',
                'inspection_date'  => Carbon::now()->subDays(rand(0, 30)),
                'description'      => "Inspeção inicial de cadastro da tecnologia {$technology->name}.",
                'user_id'          => $user->id,
            ]);

            // Opcional: segunda inspeção para simular histórico
            if (rand(0, 1)) {
                Inspection::create([
                    'inspectable_id'   => $technology->id,
                    'inspectable_type' => AssistiveTechnology::class,
                    'state'            => $conservation->value,
                    'status'           => $inspectionStatus,
                    'type'             => 'periodic',
                    'inspection_date'  => Carbon::now()->subDays(rand(1, 60)),
                    'description'      => "Inspeção periódica de rotina.",
                    'user_id'          => $user->id,
                ]);
            }
        }

        $this->command->info('15 tecnologias assistivas criadas com sucesso, todas com estoque 1 e disponíveis.');
    }
}
