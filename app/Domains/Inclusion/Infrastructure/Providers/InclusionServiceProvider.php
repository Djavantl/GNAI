<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Infrastructure\Providers;

use App\Domains\Audit\Infrastructure\AuditableEntities;
use App\Domains\Inclusion\Domain\Models\AccessibilityFeature;
use App\Domains\Inclusion\Domain\Models\AeeRecord;
use App\Domains\Inclusion\Domain\Models\AeeStudentEvaluation;
use App\Domains\Inclusion\Domain\Models\Barrier;
use App\Domains\Inclusion\Domain\Models\BarrierCategory;
use App\Domains\Inclusion\Domain\Models\Course;
use App\Domains\Inclusion\Domain\Models\Deficiency;
use App\Domains\Inclusion\Domain\Models\Discipline;
use App\Domains\Inclusion\Domain\Models\Guardian;
use App\Domains\Inclusion\Domain\Models\Inspection;
use App\Domains\Inclusion\Domain\Models\InspectionEvidence;
use App\Domains\Inclusion\Domain\Models\Institution;
use App\Domains\Inclusion\Domain\Models\Publication;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Loan;
use App\Domains\Inclusion\Domain\Models\Location;
use App\Domains\Inclusion\Domain\Models\PedagogicalRecord;
use App\Domains\Inclusion\Domain\Models\Pei;
use App\Domains\Inclusion\Domain\Models\PeiDiscipline;
use App\Domains\Inclusion\Domain\Models\Pendency;
use App\Domains\Inclusion\Domain\Models\Person;
use App\Domains\Inclusion\Domain\Models\Position;
use App\Domains\Inclusion\Domain\Models\Professional;
use App\Domains\Inclusion\Domain\Models\Semester;
use App\Domains\Inclusion\Domain\Models\Session;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentContext;
use App\Domains\Inclusion\Domain\Models\StudentCourse;
use App\Domains\Inclusion\Domain\Models\StudentDeficiency;
use App\Domains\Inclusion\Domain\Models\StudentDocument;
use App\Domains\Inclusion\Domain\Models\Tag;
use App\Domains\Inclusion\Domain\Models\Teacher;
use App\Domains\Inclusion\Domain\Models\TeacherCourseDiscipline;
use App\Domains\Inclusion\Domain\Models\TeacherGlobalPermission;
use App\Domains\Inclusion\Domain\Models\Waitlist;
use App\Domains\Inclusion\UI\Console\Commands\CheckOverdueLoans;
use Illuminate\Support\ServiceProvider;

/**
 * Provider do domínio de inclusão (atendimento educacional especializado e
 * radar inclusivo): comando de empréstimos em atraso e models auditáveis.
 */
final class InclusionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            CheckOverdueLoans::class,
        ]);

        $this->app->make(AuditableEntities::class)->register(
            AccessibilityFeature::class,
            AeeRecord::class,
            AeeStudentEvaluation::class,
            Barrier::class,
            BarrierCategory::class,
            Course::class,
            Deficiency::class,
            Discipline::class,
            Guardian::class,
            Inspection::class,
            InspectionEvidence::class,
            Institution::class,
            Publication::class,
            Item::class,
            Loan::class,
            Location::class,
            PedagogicalRecord::class,
            Pei::class,
            PeiDiscipline::class,
            Pendency::class,
            Person::class,
            Position::class,
            Professional::class,
            Semester::class,
            Session::class,
            Student::class,
            StudentContext::class,
            StudentCourse::class,
            StudentDeficiency::class,
            StudentDocument::class,
            Tag::class,
            Teacher::class,
            TeacherCourseDiscipline::class,
            TeacherGlobalPermission::class,
            Waitlist::class,
        );
    }
}
