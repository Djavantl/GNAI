<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Queries\Professionals;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use Illuminate\Support\Facades\DB;

final class ProfessionalHasLinkedRecordsQuery
{
    public function execute(Professional $professional): bool
    {
        $professionalId = $professional->getKey();
        $userId = $professional->user()->value('id');

        return DB::table('attendance_sessions')->where('professional_id', $professionalId)->exists()
            || DB::table('pendencies')->where('assigned_to', $professionalId)->exists()
            || DB::table('student_contexts')->where('evaluated_by_professional_id', $professionalId)->exists()
            || DB::table('loans')->where('professional_id', $professionalId)->exists()
            || DB::table('waitlists')->where('professional_id', $professionalId)->exists()
            || DB::table('barriers')->where('affected_professional_id', $professionalId)->exists()
            || ($userId !== null && DB::table('student_documents')->where('uploaded_by', $userId)->exists());
    }
}
