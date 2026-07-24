<?php

namespace App\Providers;

use App\Domains\Auth\Application\Queries\Permissions\UserHasPermissionQuery;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Backup\Application\Actions\PruneBackupsAction;
use App\Domains\Backup\Application\Contracts\BackupArchiveStorageContract;
use App\Domains\Backup\Application\Contracts\PruneBackupsActionContract;
use App\Domains\Backup\Infrastructure\Storage\BackupArchiveStorage;
use App\Domains\InclusiveRadar\Domain\Models\AccessibleEducationalMaterial;
use App\Domains\InclusiveRadar\Domain\Models\AssistiveTechnology;
use App\Domains\InclusiveRadar\Domain\Models\Barrier;
use App\Domains\InclusiveRadar\Domain\Models\Inspection;
use App\Domains\InclusiveRadar\Domain\Models\Institution;
use App\Models\Permission;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentContext;
use App\Models\SpecializedEducationalSupport\StudentCourse;
use App\Models\SpecializedEducationalSupport\StudentDeficiencies;
use App\Models\SpecializedEducationalSupport\StudentDocument;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(BackupArchiveStorageContract::class, BackupArchiveStorage::class);
        $this->app->bind(PruneBackupsActionContract::class, PruneBackupsAction::class);
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();
        Relation::enforceMorphMap([
            'student' => Student::class,
            'person' => Person::class,
            'student_deficiency' => StudentDeficiencies::class,
            'student_document' => StudentDocument::class,
            'student_course' => StudentCourse::class,
            'student_context' => StudentContext::class,
            'assistive_technology' => AssistiveTechnology::class,
            'accessible_educational_material' => AccessibleEducationalMaterial::class,
            'barrier' => Barrier::class,
            'inspection' => Inspection::class,
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
                return app(UserHasPermissionQuery::class)->execute($user, $permission->slug);
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
