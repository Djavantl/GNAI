<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Audit\Infrastructure\AuditableEntities;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\Models\Item;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\AeeStudentEvaluation;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Course;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Deficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Discipline;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Guardian;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PedagogicalRecord;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use App\Domains\SpecializedEducationalSupport\Domain\Models\PeiDiscipline;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pendency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Position;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Professional;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Semester;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDeficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDocument;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;
use App\Domains\SpecializedEducationalSupport\Domain\Models\TeacherCourseDiscipline;
use App\Domains\SpecializedEducationalSupport\Domain\Models\TeacherGlobalPermission;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Inertia::setRootView('inertia');
        Relation::enforceMorphMap([
            'student' => Student::class,
            'person' => Person::class,
            'student_deficiency' => StudentDeficiency::class,
            'student_document' => StudentDocument::class,
            'student_course' => StudentCourse::class,
            'student_context' => StudentContext::class,
            'item' => Item::class,
            'barrier' => Barrier::class,
            'inspection' => Inspection::class,
            'user' => User::class,
        ]);

        // O módulo de atendimento não tem provider próprio: seus models auditáveis são registrados aqui.
        $this->app->make(AuditableEntities::class)->register(
            AeeRecord::class,
            AeeStudentEvaluation::class,
            Course::class,
            Deficiency::class,
            Discipline::class,
            Guardian::class,
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
            Teacher::class,
            TeacherCourseDiscipline::class,
            TeacherGlobalPermission::class,
        );
    }
}
