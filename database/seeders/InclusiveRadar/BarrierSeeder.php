<?php

namespace Database\Seeders\InclusiveRadar;

use Illuminate\Database\Seeder;
use App\Models\InclusiveRadar\Institution;
use App\Models\InclusiveRadar\Location;
use App\Models\InclusiveRadar\BarrierCategory;
use App\Models\InclusiveRadar\Barrier;
use App\Models\User;
use App\Enums\Priority;
use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\InclusiveRadar\InspectionType;
use App\Services\InclusiveRadar\InspectionService;
use Carbon\Carbon;

class BarrierSeeder extends Seeder
{
    public function run(InspectionService $inspectionService): void
    {
        // 1. Garantir um usuário para registrar
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin Seeder',
                'email' => 'admin@seeder.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $this->command->info('Usuário admin@seeder.com criado para registro da barreira.');
        }

        // 2. Buscar a instituição
        $institution = Institution::first();
        if (!$institution) {
            $this->command->error('Nenhuma instituição encontrada. Execute InstitutionSeeder primeiro.');
            return;
        }

        // 3. Criar ou obter a localização "Dormitórios"
        $location = Location::firstOrCreate(
            [
                'institution_id' => $institution->id,
                'name' => 'Dormitórios',
            ],
            [
                'type' => 'Alojamento / Dormitório',
                'description' => 'Área dos dormitórios dos alunos.',
                'latitude' => -14.30243075,
                'longitude' => -42.69277423,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info($location->wasRecentlyCreated
            ? "Localização 'Dormitórios' criada com sucesso."
            : "Localização 'Dormitórios' já existente.");

        // 4. Buscar a categoria (Arquitetônica)
        $category = BarrierCategory::where('name', 'Arquitetônica')->first();
        if (!$category) {
            $this->command->error('Categoria "Arquitetônica" não encontrada. Execute BarrierCategorySeeder primeiro.');
            return;
        }

        // 5. Criar a barreira manualmente (apenas campos da tabela)
        $barrier = Barrier::create([
            'name'                      => 'Problema nos dormitórios',
            'description'               => 'Os dormitórios apresentam barreiras arquitetônicas que dificultam a locomoção de pessoas com deficiência física: portas estreitas, ausência de rampas e banheiros não adaptados.',
            'registered_by_user_id'     => $user->id,
            'institution_id'             => $institution->id,
            'barrier_category_id'        => $category->id,
            'location_id'                => $location->id,
            'latitude'                   => $location->latitude,
            'longitude'                  => $location->longitude,
            'priority'                   => Priority::HIGH->value,
            'identified_at'              => Carbon::now()->subDays(15)->format('Y-m-d'),
            'is_anonymous'                => false,
            'not_applicable'              => false,
            'is_active'                   => true,
        ]);

        $this->command->info("Barreira '{$barrier->name}' criada com ID {$barrier->id}.");

        // 6. (Opcional) Criar uma inspeção inicial para a barreira
        $inspectionService->createForModel($barrier, [
            'status'          => BarrierStatus::IDENTIFIED->value,
            'inspection_date' => Carbon::now()->subDays(15)->format('Y-m-d'),
            'type'            => InspectionType::INITIAL->value,
            'description'     => 'Identificação inicial da barreira nos dormitórios durante vistoria de acessibilidade.',
            'images'          => [], // sem imagens por enquanto
        ]);

        $this->command->info("Inspeção inicial registrada para a barreira.");

        // 7. Se desejar associar deficiências, faça aqui (exemplo com IDs existentes)
        // $barrier->deficiencies()->sync([1, 2, 3]); // IDs das deficiências
    }
}
