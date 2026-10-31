<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\Models\Barrier;
use App\Domains\Inclusion\Domain\Models\Inspection;
use App\Domains\Inclusion\Domain\Models\Item;
use App\Domains\Inclusion\Domain\Models\Person;
use App\Domains\Inclusion\Domain\Models\Student;
use App\Domains\Inclusion\Domain\Models\StudentContext;
use App\Domains\Inclusion\Domain\Models\StudentCourse;
use App\Domains\Inclusion\Domain\Models\StudentDeficiency;
use App\Domains\Inclusion\Domain\Models\StudentDocument;
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
    }
}
