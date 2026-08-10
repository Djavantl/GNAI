<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Person;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentContext;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDeficiency;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDocument;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Relation::enforceMorphMap([
            'student' => Student::class,
            'person' => Person::class,
            'student_deficiency' => StudentDeficiency::class,
            'student_document' => StudentDocument::class,
            'student_course' => StudentCourse::class,
            'student_context' => StudentContext::class,
            'assistive_technology' => AssistiveTechnology::class,
            'accessible_educational_material' => AccessibleEducationalMaterial::class,
            'barrier' => Barrier::class,
            'inspection' => Inspection::class,
            'user' => User::class,
        ]);
    }
}
