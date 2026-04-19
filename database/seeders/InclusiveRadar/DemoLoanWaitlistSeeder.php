<?php

namespace Database\Seeders\InclusiveRadar;

use App\Enums\InclusiveRadar\ConservationState;
use App\Enums\InclusiveRadar\LoanStatus;
use App\Enums\InclusiveRadar\ResourceStatus;
use App\Enums\InclusiveRadar\WaitlistStatus;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DemoLoanWaitlistSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $user = User::firstOrFail();

            $students = Student::orderBy('id')->get();
            if ($students->count() < 2) {
                throw new \RuntimeException('É necessário ter pelo menos 2 alunos cadastrados.');
            }

            $deficiencyIds = DB::table('deficiencies')->pluck('id')->toArray();
            $featureIds = DB::table('accessibility_features')->pluck('id')->toArray();

            $studentA = $students->get(0);
            $studentB = $students->get(1);
            $studentC = $students->get(2) ?? $students->get(0);
            $studentD = $students->get(3) ?? $students->get(1);
            $studentE = $students->get(4) ?? $students->get(0);
            $studentF = $students->get(5) ?? $students->get(1);

            /*
            |--------------------------------------------------------------------------
            | ITEM 1
            |--------------------------------------------------------------------------
            */
            $item1 = AccessibleEducationalMaterial::updateOrCreate(
                ['name' => 'Leitor Portátil com Áudio'],
                [
                    'asset_code'         => 'AEM-LOAN-001',
                    'is_digital'         => false,
                    'quantity'           => 1,
                    'quantity_available' => 0,
                    'conservation_state' => ConservationState::GOOD->value,
                    'is_loanable'        => true,
                    'status'             => ResourceStatus::IN_USE->value,
                    'is_active'          => true,
                ]
            );

            $this->attachRandomRelations($item1, $deficiencyIds, $featureIds);

            $this->createLoan(
                $item1,
                $studentA->id,
                $user->id,
                now()->subDays(10),
                now()->subDays(5),
                'Devolvido no prazo.',
                LoanStatus::RETURNED,
                now()->subDays(5)
            );

            $this->createLoan(
                $item1,
                $studentB->id,
                $user->id,
                now()->subDays(2),
                now()->addDays(5),
                'Empréstimo atual.',
                LoanStatus::ACTIVE
            );

            $this->createWaitlist($item1, $studentC->id, $user->id, now()->subDays(2)->setTime(8, 0), 'Fila 1');
            $this->createWaitlist($item1, $studentD->id, $user->id, now()->subDay()->setTime(10, 0), 'Fila 2');

            $this->createWaitlist(
                $item1,
                $studentE->id,
                $user->id,
                now()->subDay()->setTime(12, 0),
                'Cancelado',
                WaitlistStatus::CANCELLED
            );

            /*
            |--------------------------------------------------------------------------
            | ITEM 2
            |--------------------------------------------------------------------------
            */
            $item2 = AccessibleEducationalMaterial::updateOrCreate(
                ['name' => 'Mapa Tátil'],
                [
                    'asset_code'         => 'AEM-LOAN-002',
                    'is_digital'         => false,
                    'quantity'           => 1,
                    'quantity_available' => 0,
                    'conservation_state' => ConservationState::GOOD->value,
                    'is_loanable'        => true,
                    'status'             => ResourceStatus::IN_USE->value,
                    'is_active'          => true,
                ]
            );

            $this->attachRandomRelations($item2, $deficiencyIds, $featureIds);

            $this->createLoan(
                $item2,
                $studentF->id,
                $user->id,
                now()->subDays(8),
                now()->subDays(3),
                'Devolvido com atraso.',
                LoanStatus::LATE,
                now()->subDay()
            );

            $this->createWaitlist($item2, $studentA->id, $user->id, now()->subDays(3), 'Fila item 2');

            /*
            |--------------------------------------------------------------------------
            | ITEM 3 (Digital)
            |--------------------------------------------------------------------------
            */
            $item3 = AccessibleEducationalMaterial::updateOrCreate(
                ['name' => 'Software Leitor de Tela'],
                [
                    'asset_code'         => 'AEM-DIG-001',
                    'is_digital'         => true,
                    'quantity'           => 999,
                    'quantity_available' => 999,
                    'conservation_state' => ConservationState::NOT_APPLICABLE->value,
                    'is_loanable'        => true,
                    'status'             => ResourceStatus::AVAILABLE->value,
                    'is_active'          => true,
                ]
            );

            $this->attachRandomRelations($item3, $deficiencyIds, $featureIds);

            $this->createLoan($item3, $studentB->id, $user->id, now()->subDay(), now()->addDays(10), 'Digital', LoanStatus::ACTIVE);
            $this->createLoan($item3, $studentC->id, $user->id, now()->subDays(2), now()->addDays(8), 'Digital 2', LoanStatus::ACTIVE);

            /*
            |--------------------------------------------------------------------------
            | ITEM 4
            |--------------------------------------------------------------------------
            */
            $item4 = AccessibleEducationalMaterial::updateOrCreate(
                ['name' => 'Livro em Braille'],
                [
                    'asset_code'         => 'AEM-LOAN-003',
                    'is_digital'         => false,
                    'quantity'           => 1,
                    'quantity_available' => 0,
                    'conservation_state' => ConservationState::GOOD->value,
                    'is_loanable'        => true,
                    'status'             => ResourceStatus::IN_USE->value,
                    'is_active'          => true,
                ]
            );

            $this->attachRandomRelations($item4, $deficiencyIds, $featureIds);

            $this->createLoan(
                $item4,
                $studentD->id,
                $user->id,
                now()->subDays(12),
                now()->subDays(6),
                'Devolvido normal.',
                LoanStatus::RETURNED,
                now()->subDays(6)
            );

            $this->createLoan(
                $item4,
                $studentE->id,
                $user->id,
                now()->subDays(1),
                now()->addDays(4),
                'Novo empréstimo.',
                LoanStatus::ACTIVE
            );

            $this->createWaitlist($item4, $studentF->id, $user->id, now()->subDays(2), 'Fila');

            $this->command->info('Seeder atualizado com múltiplos status.');
        });
    }

    private function attachRandomRelations($item, $deficiencyIds, $featureIds): void
    {
        if ($deficiencyIds) {
            $item->deficiencies()->sync(collect($deficiencyIds)->random(min(2, count($deficiencyIds))));
        }

        if ($featureIds) {
            $item->accessibilityFeatures()->sync(collect($featureIds)->random(min(2, count($featureIds))));
        }
    }

    private function createLoan(
        $item,
        int $studentId,
        int $userId,
        Carbon $loanDate,
        Carbon $dueDate,
        string $obs,
        LoanStatus $status,
        ?Carbon $returnDate = null
    ): void {
        $item->loans()->updateOrCreate(
            [
                'student_id' => $studentId,
                'loan_date'  => $loanDate,
            ],
            [
                'user_id'      => $userId,
                'due_date'     => $dueDate,
                'return_date'  => $returnDate,
                'status'       => $status->value,
                'observation'  => $obs,
            ]
        );
    }

    private function createWaitlist(
        $item,
        int $studentId,
        int $userId,
        Carbon $date,
        string $obs,
        WaitlistStatus $status = WaitlistStatus::WAITING
    ): void {
        $item->waitlists()->updateOrCreate(
            [
                'student_id'   => $studentId,
                'requested_at' => $date,
            ],
            [
                'user_id'     => $userId,
                'status'      => $status->value,
                'observation' => $obs,
            ]
        );
    }
}
