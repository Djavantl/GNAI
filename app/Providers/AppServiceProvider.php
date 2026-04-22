<?php

namespace App\Providers;

use App\Models\InclusiveRadar\Institution;
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
use Throwable;
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
            'user' => User::class,
        ]);

        Gate::before(function ($user) {
            if ($user->is_admin) {
                return true;
            }

            return null;
        });

        $this->registerPermissionGates();

        // View Composer para a Navbar (INSTITUIÇÃO)
        View::composer('layouts.master', function ($view) {
            $view->with('institution', $this->resolveInstitutionForLayout());
        });
    }

    /**
     * RF: registra permissões dinâmicas sem impedir o bootstrap em build, CI ou banco indisponível.
     * Uso: autorização via Gate em toda a aplicação após migrations e banco acessível.
     */
    private function registerPermissionGates(): void
    {
        if (! $this->canUseTable('permissions')) {
            return;
        }

        foreach (Permission::query()->get(['slug']) as $permission) {
            Gate::define($permission->slug, function ($user) use ($permission) {
                return $user->hasPermission($permission->slug);
            });
        }
    }

    /**
     * RF: fornece a instituição padrão para a navbar sem acoplar o boot ao banco.
     * Uso: layout principal e páginas administrativas que exibem a identificação institucional.
     */
    private function resolveInstitutionForLayout(): ?Institution
    {
        if (! $this->canUseTable('institutions')) {
            return null;
        }

        return Institution::query()->first();
    }

    /**
     * RF: evita falhas de bootstrap quando o banco ainda não existe, não respondeu ou a tabela não foi criada.
     * Uso: guards de providers, builds Docker, package discovery, comandos artisan e ambientes recém-provisionados.
     */
    private function canUseTable(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (Throwable) {
            return false;
        }
    }
}
