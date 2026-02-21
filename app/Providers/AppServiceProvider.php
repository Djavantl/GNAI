<?php

namespace App\Providers;

use App\Models\InclusiveRadar\ResourceType;
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
        ]);

        // --- SISTEMA DE PERMISSÕES ---
        // Verifica se a tabela existe para evitar erros em novas instalações/migrations
        if (Schema::hasTable('permissions')) {
            try {

                // ADMIN TEM TODAS PERMISSÕES
                Gate::before(function ($user, $ability) {
                    if ($user->is_admin) {
                        return true;
                    }
                });

                $permissions = Permission::all();

                foreach ($permissions as $permission) {
                    Gate::define($permission->slug, function ($user) use ($permission) {
                        return $user->hasPermission($permission->slug);
                    });
                }

            } catch (\Exception $e) {
                // Silencia erros
            }
        }

        // View Composer para Accessible Educational Materials
        View::composer(['pages.inclusive-radar.accessible-educational-materials.create',
            'pages.inclusive-radar.accessible-educational-materials.edit',
        ], function ($view) {
            $view->with([
                'deficiencies' => Deficiency::orderBy('name')->get(),
                'resourceTypes' => ResourceType::active()
                    ->forEducationalMaterial()
                    ->orderBy('name')
                    ->get(),
            ]);
        });

        // View Composer para Assistive Technologies (se quiser fazer o mesmo)
        View::composer([
            'pages.inclusive-radar.assistive-technologies.create',
            'pages.inclusive-radar.assistive-technologies.edit',
        ], function ($view) {
            $view->with([
                'deficiencies' => Deficiency::orderBy('name')->get(),
                'resourceTypes' => ResourceType::active()
                    ->forAssistiveTechnology()
                    ->orderBy('name')
                    ->get(),
            ]);
        });
    }
}
