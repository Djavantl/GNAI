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
            $studentLoan = Student::firstOrFail();
            $studentWaitlist = Student::skip(1)->first() ?? $studentLoan;
            $deficiencyIds = DB::table('deficiencies')->pluck('id')->toArray();
            $featureIds = DB::table('accessibility_features')->pluck('id')->toArray();
            $item = AccessibleEducationalMaterial::updateOrCreate(
                ['asset_code' => 'AEM-LOAN-001'],
                [
                    'name' => 'Leitor Portátil com Áudio',
                    'is_digital' => false,
                    'notes' => 'Item indisponível com empréstimo ativo e fila de espera.',
                    'quantity' => 1,
                    'quantity_available' => 0,
                    'conservation_state' => ConservationState::GOOD->value,
                    'is_loanable' => true,
                    'status' => ResourceStatus::IN_USE->value,
                    'is_active' => true,
                ]
            );

            if (!empty($deficiencyIds)) {
                $item->deficiencies()->sync(
                    collect($deficiencyIds)->random(min(2, count($deficiencyIds)))->toArray()
                );
            }

            if (!empty($featureIds)) {
                $item->accessibilityFeatures()->sync(
                    collect($featureIds)->random(min(2, count($featureIds)))->toArray()
                );
            }

            $item->loans()->updateOrCreate(
                [
                    'student_id' => $studentLoan->id,
                    'status' => LoanStatus::ACTIVE->value,
                ],
                [
                    'professional_id' => null,
                    'user_id' => $user->id,
                    'loan_date' => now()->subDays(2),
                    'due_date' => now()->addDays(5),
                    'return_date' => null,
                    'observation' => 'Empréstimo automático para item sem estoque.',
                ]
            );

            $item->waitlists()->updateOrCreate(
                [
                    'student_id' => $studentWaitlist->id,
                    'status' => WaitlistStatus::WAITING->value,
                ],
                [
                    'professional_id' => null,
                    'user_id' => $user->id,
                    'requested_at' => now()->subDay(),
                    'observation' => 'Usuário aguardando devolução do item.',
                ]
            );

        });
    }
}
