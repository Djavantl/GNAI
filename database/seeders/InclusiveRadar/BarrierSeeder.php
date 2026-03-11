<?php

namespace Database\Seeders\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\InclusiveRadar\InspectionType;
use App\Enums\Priority;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\BarrierCategory;
use App\Models\InclusiveRadar\Institution;
use App\Models\InclusiveRadar\Location;
use App\Models\InclusiveRadar\Inspection;
use App\Models\InclusiveRadar\InspectionImage;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\User;
use App\Services\InclusiveRadar\BarrierService;
use App\Services\InclusiveRadar\InspectionService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarrierSeeder extends Seeder
{
    protected BarrierService $barrierService;
    protected InspectionService $inspectionService;

    public function __construct(BarrierService $barrierService, InspectionService $inspectionService)
    {
        $this->barrierService = $barrierService;
        $this->inspectionService = $inspectionService;
    }

    public function run(): void
    {
        DB::transaction(function () {
            // Garantir dados base (chame seeders existentes se necessário)
            $this->callIfEmpty('institutions', \Database\Seeders\InclusiveRadar\InstitutionSeeder::class ?? null);
            $this->callIfEmpty('barrier_categories', \Database\Seeders\InclusiveRadar\BarrierCategorySeeder::class ?? null);
            $this->callIfEmpty('deficiencies', \Database\Seeders\SpecializedEducationalSupport\DeficiencySeeder::class ?? null);
            $this->callIfEmpty('people', \Database\Seeders\SpecializedEducationalSupport\ProfessionalSeeder::class ?? null);
            $this->callIfEmpty('students', \Database\Seeders\SpecializedEducationalSupport\StudentSeeder::class ?? null);

            $institution = Institution::first();
            if (!$institution) {
                $this->command->error('Nenhuma instituição encontrada. Rode os seeders de Institution primeiro.');
                return;
            }

            // Garante que os locais usados existam (idempotente)
            $locations = $this->ensureLocations($institution);

            // Garante usuários/reporters usados no SELECT: cria se não existir
            $reporters = $this->ensureReporters();

            // Mapeamento de deficiências pelo nome
            $defMap = DB::table('deficiencies')->pluck('id', 'name')->toArray();

            // Dados das 6 barreiras com inspeções variadas (datas fixas para reproduzir seu SELECT)
            $barriers = [
                // 1
                [
                    'name' => 'Necessidade de adequação Campo de Futebol',
                    'description' => 'O campo de futebol apresenta barreiras arquitetônicas: gramado irregular, ausência de piso tátil e acesso inadequado para cadeirantes.',
                    'registered_by' => $reporters['joao-santos'],
                    'institution_id' => $institution->id,
                    'category' => 'Arquitetônica',
                    'location' => $locations['campo']->id,
                    'latitude' => -14.30339992,
                    'longitude' => -42.69446329,
                    'identified_at' => Carbon::create(2025,10,1),
                    'resolved_at' => null,
                    'priority' => Priority::HIGH->value,
                    'is_active' => true,
                    'is_anonymous' => false,
                    'deficiencies' => ['Visual','Física'],
                    'affected_student_name' => 'Marley', // tentamos vincular
                    'affected_professional_name' => 'Adriany Oliveira', // tentamos vincular
                    'inspections' => [
                        ['status' => BarrierStatus::IDENTIFIED, 'date' => Carbon::create(2025,10,1), 'type' => InspectionType::INITIAL],
                        ['status' => BarrierStatus::UNDER_ANALYSIS, 'date' => Carbon::create(2025,11,1), 'type' => InspectionType::PERIODIC],
                        ['status' => BarrierStatus::IN_PROGRESS, 'date' => Carbon::create(2026,1,1), 'type' => InspectionType::MAINTENANCE],
                    ],
                ],
                // 2
                [
                    'name' => 'LAB1 - Aula 2 Problemas Comunicacionais',
                    'description' => 'No Laboratório 1 de ADS, faltam recursos de comunicação acessível: softwares de leitura de tela não instalados, placas informativas sem braile e ausência de intérprete de Libras para aulas práticas.',
                    'registered_by' => $reporters['marcos-palmeira'],
                    'institution_id' => $institution->id,
                    'category' => 'Comunicacional',
                    'location' => $locations['lab1']->id,
                    'latitude' => -14.30228232,
                    'longitude' => -42.69311971,
                    'identified_at' => Carbon::create(2026,1,6),
                    'resolved_at' => Carbon::create(2026,3,11),
                    'priority' => Priority::MEDIUM->value,
                    'is_active' => true,
                    'is_anonymous' => false,
                    'deficiencies' => ['Intelectual','Psicossocial'],
                    'affected_person_name' => 'Alexa Pires Filho',
                    'affected_person_role' => 'Mãe do Aluno Pedro Henrique',
                    'inspections' => [
                        ['status' => BarrierStatus::IDENTIFIED, 'date' => Carbon::create(2026,1,6), 'type' => InspectionType::INITIAL],
                    ],
                ],
                // 3
                [
                    'name' => 'Problema com a Quadra de Futsal',
                    'description' => 'A quadra de futsal possui barreiras físicas que impedem o uso por cadeirantes: portas estreitas, falta de rampa de acesso à quadra e banheiros não adaptados.',
                    'registered_by' => $reporters['admin-gnai'],
                    'institution_id' => $institution->id,
                    'category' => 'Arquitetônica',
                    'location' => $locations['quadra']->id,
                    'latitude' => -14.30251363,
                    'longitude' => -42.69403173,
                    'identified_at' => Carbon::create(2025,6,26),
                    'resolved_at' => null,
                    'priority' => Priority::HIGH->value,
                    'is_active' => true,
                    'is_anonymous' => false,
                    'deficiencies' => ['Física','Intelectual'],
                    'inspections' => [
                        ['status' => BarrierStatus::IDENTIFIED, 'date' => Carbon::create(2025,6,26), 'type' => InspectionType::INITIAL],
                    ],
                ],
                // 4
                [
                    'name' => 'Dificuldade de acesso em Quadra de Futsal (caso 2)',
                    'description' => 'Além das barreiras já conhecidas, a quadra tem piso escorregadio e iluminação insuficiente, agravando os riscos para pessoas com mobilidade reduzida.',
                    'registered_by' => $reporters['admin-gnai'],
                    'institution_id' => $institution->id,
                    'category' => 'Arquitetônica',
                    'location' => $locations['quadra']->id,
                    'latitude' => -14.30248151,
                    'longitude' => -42.69396515,
                    'identified_at' => Carbon::create(2025,11,21),
                    'resolved_at' => Carbon::create(2026,3,11),
                    'priority' => Priority::MEDIUM->value,
                    'is_active' => true,
                    'is_anonymous' => false,
                    'deficiencies' => ['Auditiva','Intelectual','Psicossocial'],
                    'inspections' => [
                        ['status' => BarrierStatus::IDENTIFIED, 'date' => Carbon::create(2025,11,21), 'type' => InspectionType::INITIAL],
                        ['status' => BarrierStatus::UNDER_ANALYSIS, 'date' => Carbon::create(2025,12,21), 'type' => InspectionType::PERIODIC],
                        ['status' => BarrierStatus::IN_PROGRESS, 'date' => Carbon::create(2026,2,15), 'type' => InspectionType::MAINTENANCE],
                    ],
                ],
                // 5
                [
                    'name' => 'Dificuldade de acesso em Biblioteca',
                    'description' => 'A biblioteca central apresenta corredores estreitos entre estantes, mesas fixas com altura inadequada para cadeirantes e ausência de sinalização tátil.',
                    'registered_by' => $reporters['tais-araujo'],
                    'institution_id' => $institution->id,
                    'category' => 'Arquitetônica',
                    'location' => $locations['biblioteca']->id,
                    'latitude' => -14.30150148,
                    'longitude' => -42.69391913,
                    'identified_at' => Carbon::create(2026,2,4),
                    'resolved_at' => null,
                    'priority' => Priority::MEDIUM->value,
                    'is_active' => true,
                    'is_anonymous' => false,
                    'deficiencies' => ['Auditiva','Física','Intelectual'],
                    'inspections' => [
                        ['status' => BarrierStatus::IDENTIFIED, 'date' => Carbon::create(2026,2,4), 'type' => InspectionType::INITIAL],
                    ],
                ],
                // 6
                [
                    'name' => 'Alunos do Médio enfrentam problemas Atitudinais',
                    'description' => 'Relatos de tratamento discriminatório por parte de funcionários e alunos no Pavilhão do Médio, incluindo recusa de auxílio a pessoas com deficiência e comentários inadequados.',
                    'registered_by' => $reporters['gloria-pires'],
                    'institution_id' => $institution->id,
                    'category' => 'Atitudinal',
                    'location' => $locations['pavilhao']->id,
                    'latitude' => -14.30191694,
                    'longitude' => -42.69329488,
                    'identified_at' => Carbon::create(2025,10,30),
                    'resolved_at' => null,
                    'priority' => Priority::HIGH->value,
                    'is_active' => true,
                    'is_anonymous' => false,
                    'deficiencies' => ['Física'],
                    'inspections' => [
                        ['status' => BarrierStatus::IDENTIFIED, 'date' => Carbon::create(2025,10,30), 'type' => InspectionType::INITIAL],
                        ['status' => BarrierStatus::UNDER_ANALYSIS, 'date' => Carbon::create(2025,12,1), 'type' => InspectionType::PERIODIC],
                        ['status' => BarrierStatus::IN_PROGRESS, 'date' => Carbon::create(2026,1,30), 'type' => InspectionType::PERIODIC],
                    ],
                ],
                // 7 - Em Análise
                [
                    'name' => 'Rampa de acesso à Biblioteca',
                    'description' => 'Rampa apresenta inclinação inadequada e falta de corrimão lateral.',
                    'registered_by' => $reporters['joao-santos'],
                    'institution_id' => $institution->id,
                    'category' => 'Arquitetônica',
                    'location' => $locations['biblioteca']->id,
                    'latitude' => -14.301600,
                    'longitude' => -42.693900,
                    'identified_at' => Carbon::create(2026,2,10),
                    'resolved_at' => null,
                    'priority' => Priority::MEDIUM->value,
                    'is_active' => true,
                    'is_anonymous' => false,
                    'deficiencies' => ['Física'],
                    'inspections' => [
                        ['status' => BarrierStatus::UNDER_ANALYSIS, 'date' => Carbon::create(2026,2,15), 'type' => InspectionType::INITIAL],
                    ],
                ],
                // 8 - Resolvida
                [
                    'name' => 'Banheiro adaptado do Bloco C',
                    'description' => 'Banheiro adaptado com barras de apoio instaladas e altura adequada para cadeirantes.',
                    'registered_by' => $reporters['marcos-palmeira'],
                    'institution_id' => $institution->id,
                    'category' => 'Arquitetônica',
                    'location' => null,
                    'latitude' => -14.30188055,
                    'longitude' => -42.69303213,
                    'identified_at' => Carbon::create(2025,12,1),
                    'resolved_at' => Carbon::create(2026,2,1),
                    'priority' => Priority::LOW->value,
                    'is_active' => true,
                    'is_anonymous' => false,
                    'deficiencies' => ['Física','Visual'],
                    'inspections' => [
                        ['status' => BarrierStatus::IDENTIFIED, 'date' => Carbon::create(2025,12,1), 'type' => InspectionType::INITIAL],
                        ['status' => BarrierStatus::RESOLVED, 'date' => Carbon::create(2026,2,1), 'type' => InspectionType::MAINTENANCE],
                    ],
                ],
                // 10 - Em Análise
                [
                    'name' => 'Corredores do Bloco A',
                    'description' => 'Corredores estreitos dificultam a passagem de cadeirantes durante horários de pico.',
                    'registered_by' => $reporters['tais-araujo'],
                    'institution_id' => $institution->id,
                    'category' => 'Arquitetônica',
                    'location' => null,
                    'latitude' => -14.30183377,
                    'longitude' => -42.69346666,
                    'identified_at' => Carbon::create(2026,2,20),
                    'resolved_at' => null,
                    'priority' => Priority::HIGH->value,
                    'is_active' => true,
                    'is_anonymous' => false,
                    'deficiencies' => ['Física','Auditiva'],
                    'inspections' => [
                        ['status' => BarrierStatus::UNDER_ANALYSIS, 'date' => Carbon::create(2026,2,22), 'type' => InspectionType::INITIAL],
                    ],
                ],
                // 11 - Resolvida
                [
                    'name' => 'Acesso à Sala de Informática',
                    'description' => 'Rampa instalada e altura das mesas ajustada para cadeirantes.',
                    'registered_by' => $reporters['gloria-pires'],
                    'institution_id' => $institution->id,
                    'category' => 'Arquitetônica',
                    'location' => null,
                    'latitude' => -14.30229438,
                    'longitude' => -42.69324099,
                    'identified_at' => Carbon::create(2025,11,15),
                    'resolved_at' => Carbon::create(2026,1,15),
                    'priority' => Priority::MEDIUM->value,
                    'is_active' => true,
                    'is_anonymous' => false,
                    'deficiencies' => ['Física'],
                    'inspections' => [
                        ['status' => BarrierStatus::IDENTIFIED, 'date' => Carbon::create(2025,11,15), 'type' => InspectionType::INITIAL],
                        ['status' => BarrierStatus::NOT_APPLICABLE, 'date' => Carbon::create(2026,1,15), 'type' => InspectionType::MAINTENANCE],
                    ],
                ],
            ];

            foreach ($barriers as $bData) {
                // category id
                $category = BarrierCategory::where('name', $bData['category'])->first();
                if (!$category) {
                    $this->command->warn("Categoria {$bData['category']} não encontrada — pulando '{$bData['name']}'");
                    continue;
                }

                // create or update barrier (idempotente por name + location)
                $barrier = Barrier::updateOrCreate(
                    ['name' => $bData['name'], 'location_id' => $bData['location']],
                    [
                        'description' => $bData['description'],
                        'registered_by_user_id' => $bData['registered_by']->id ?? null,
                        'institution_id' => $bData['institution_id'],
                        'barrier_category_id' => $category->id,
                        'latitude' => $bData['latitude'],
                        'longitude' => $bData['longitude'],
                        'identified_at' => $bData['identified_at']->format('Y-m-d'),
                        'resolved_at' => $bData['resolved_at'] ? $bData['resolved_at']->format('Y-m-d') : null,
                        'priority' => $bData['priority'],
                        'is_active' => $bData['is_active'],
                        'is_anonymous' => $bData['is_anonymous'],
                        'location_specific_details' => $bData['description'],
                    ]
                );

                // Vincular deficiências
                $defIds = [];
                foreach ($bData['deficiencies'] as $defName) {
                    if (isset($defMap[$defName])) {
                        $defIds[] = $defMap[$defName];
                    }
                }
                if (!empty($defIds)) {
                    $barrier->deficiencies()->sync($defIds);
                }

                // Tentar vincular affected_student pelo nome (opcional)
                if (!empty($bData['affected_student_name'] ?? null)) {
                    $person = Person::where('name', 'like', '%' . $bData['affected_student_name'] . '%')->first();
                    if ($person) {
                        $student = Student::where('person_id', $person->id)->first();
                        if ($student) {
                            $barrier->affected_student_id = $student->id;
                        }
                    }
                }

                // Tentar vincular affected_professional pelo nome (opcional)
                if (!empty($bData['affected_professional_name'] ?? null)) {
                    $person = Person::where('name', 'like', '%' . $bData['affected_professional_name'] . '%')->first();
                    if ($person) {
                        $professional = Professional::where('person_id', $person->id)->first();
                        if ($professional) {
                            $barrier->affected_professional_id = $professional->id;
                        }
                    }
                }

                // affected person name / role
                if (!empty($bData['affected_person_name'] ?? null)) {
                    $barrier->affected_person_name = $bData['affected_person_name'];
                    $barrier->affected_person_role = $bData['affected_person_role'] ?? null;
                }

                $barrier->save();

                // Criar inspeções garantidas: se já existirem com as mesmas datas/status, não duplicar
                foreach ($bData['inspections'] as $insData) {
                    $exists = $barrier->inspections()
                        ->where('inspection_date', $insData['date']->format('Y-m-d'))
                        ->where('status', $insData['status']->value)
                        ->exists();

                    if (!$exists) {
                        // Usa InspectionService para manter lógica de imagens/estado
                        $this->inspectionService->createForModel($barrier, [
                            'status' => $insData['status']->value,
                            'inspection_date' => $insData['date']->format('Y-m-d'),
                            'type' => $insData['type']->value,
                            'description' => $this->inspectionDescription($insData['status']),
                            'images' => [],
                        ]);
                    }
                }

                $this->command->info("Barrier seeded/updated: {$barrier->id} - {$barrier->name}");
            }
        });
    }

    private function callIfEmpty(string $table, ?string $seederClass): void
    {
        if (DB::table($table)->count() === 0 && $seederClass) {
            $this->call($seederClass);
        }
    }

    private function ensureLocations(Institution $institution): array
    {
        $locations = [];
        $locations['campo'] = Location::firstOrCreate(
            ['institution_id' => $institution->id, 'name' => 'Campo de Futebol'],
            ['type'=>'Campo Esportivo','description'=>'Campo aberto','latitude'=>-14.30339992,'longitude'=>-42.69446329,'is_active'=>true]
        );
        $locations['lab1'] = Location::firstOrCreate(
            ['institution_id' => $institution->id, 'name' => 'Laboratório 1 - ADS'],
            ['type'=>'Laboratório de Informática','description'=>'Lab ADS','latitude'=>-14.30228232,'longitude'=>-42.69311971,'is_active'=>true]
        );
        $locations['quadra'] = Location::firstOrCreate(
            ['institution_id' => $institution->id, 'name' => 'Quadra de Futsal'],
            ['type'=>'Quadra Esportiva','description'=>'Quadra','latitude'=>-14.30251363,'longitude'=>-42.69403173,'is_active'=>true]
        );
        $locations['biblioteca'] = Location::firstOrCreate(
            ['institution_id' => $institution->id, 'name' => 'Biblioteca'],
            ['type'=>'Biblioteca','description'=>'Biblioteca central','latitude'=>-14.30159263,'longitude'=>-42.69390624,'is_active'=>true]
        );
        $locations['pavilhao'] = Location::firstOrCreate(
            ['institution_id' => $institution->id, 'name' => 'Pavilhão do Médio'],
            ['type'=>'Pavilhão / Bloco Didático','description'=>'Pavilhão Médio','latitude'=>-14.30191694,'longitude'=>-42.69329488,'is_active'=>true]
        );

        return $locations;
    }

    /**
     * Garante que existam usuários que usamos como "reporters" no dataset.
     * Retorna array indexado por chave.
     */
    private function ensureReporters(): array
    {
        $map = [
            'joao-santos' => ['name' => 'João Santos', 'email' => 'joao.santos@example.com'],
            'marcos-palmeira' => ['name' => 'Marcos Palmeira', 'email' => 'marcos.palmeira@example.com'],
            'admin-gnai' => ['name' => 'Admin GNAI', 'email' => 'admin@gai.local'],
            'tais-araujo' => ['name' => 'Taís Araújo', 'email' => 'tais.araujo@example.com'],
            'gloria-pires' => ['name' => 'Glória Pires', 'email' => 'gloria.pires@example.com'],
        ];

        $result = [];
        foreach ($map as $key => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('secret123'),
                    'email_verified_at' => now(),
                    'role' => $key === 'admin-gnai' ? 'admin' : 'professional',
                ]
            );
            $result[$key] = $user;
        }

        return $result;
    }

    private function inspectionDescription(BarrierStatus $status): string
    {
        return match ($status) {
            BarrierStatus::IDENTIFIED => 'Identificação inicial da barreira.',
            BarrierStatus::UNDER_ANALYSIS => 'Barreira em análise.',
            BarrierStatus::IN_PROGRESS => 'Intervenção em andamento.',
            BarrierStatus::RESOLVED => 'Barreira resolvida.',
            default => 'Vistoria.',
        };
    }
}
