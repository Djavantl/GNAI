<?php

namespace App\Providers;

use App\Models\InclusiveRadar\Institution;
use App\Models\SpecializedEducationalSupport\Deficiency;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\Person;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\InclusiveRadar\AssistiveTechnology;
use App\Models\InclusiveRadar\AccessibleEducationalMaterial;
use App\Models\InclusiveRadar\Barrier;
use App\Models\InclusiveRadar\Inspection;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use App\Models\SpecializedEducationalSupport\StudentDeficiencies;
use App\Models\SpecializedEducationalSupport\StudentDocument;
use App\Models\SpecializedEducationalSupport\StudentCourse;
use App\Models\SpecializedEducationalSupport\StudentContext;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        Relation::enforceMorphMap([
            'student'            => Student::class,
            'person'             => Person::class,
            'student_deficiency' => StudentDeficiencies::class,
            'student_document'   => StudentDocument::class,
            'student_course'     => StudentCourse::class,
            'student_context'    => StudentContext::class,
            'assistive_technology'            => AssistiveTechnology::class,
            'accessible_educational_material' => AccessibleEducationalMaterial::class,
            'barrier'                         => Barrier::class,
            'inspection'                      => Inspection::class,
            'user'                            => User::class,
        ]);

        // PROTEÇÃO: Só executa se NÃO estiver rodando via linha de comando (CLI/Migrations)
        if (!$this->app->runningInConsole()) {

            // --- SISTEMA DE PERMISSÕES ---
            if (Schema::hasTable('permissions')) {
                try {
                    Gate::before(function ($user, $ability) {
                        if ($user->is_admin) return true;
                    });

                    $permissions = Permission::all();
                    foreach ($permissions as $permission) {
                        Gate::define($permission->slug, function ($user) use ($permission) {
                            return $user->hasPermission($permission->slug);
                        });
                    }
                } catch (\Exception $e) {
                    // Silencia erros de conexão temporária
                }
            }

            // --- VIEW COMPOSERS (Também protegidos) ---
            View::composer(['pages.inclusive-radar.accessible-educational-materials.*'], function ($view) {
                $view->with('deficiencies', Deficiency::orderBy('name')->get());
            });

            View::composer(['pages.inclusive-radar.assistive-technologies.*'], function ($view) {
                $view->with('deficiencies', Deficiency::orderBy('name')->get());
            });

            View::composer('layouts.master', function ($view) {
                $institution = cache()->remember('institution_data', 86400, function () {
                    // Usamos o optional() ou null coalescing para evitar quebra se a tabela estiver vazia
                    return Institution::first() ?? new Institution();
                });
                $view->with('institution', $institution);
            });
        }
    }
}
