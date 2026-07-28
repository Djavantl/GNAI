<?php

namespace App\Providers;

use App\Domains\Auth\Application\Permissions\PermissionCache;
use App\Domains\Auth\Application\Permissions\PermissionRegistry;
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
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentCourse;
use App\Domains\SpecializedEducationalSupport\Domain\Models\StudentDeficiency;
use App\Models\SpecializedEducationalSupport\Person;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\SpecializedEducationalSupport\StudentContext;
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
        $this->app->scoped(PermissionCache::class);
        $this->app->scoped(PermissionRegistry::class);
        $this->app->scoped(UserHasPermissionQuery::class);
    }

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

        Gate::before(function ($user, string $ability) {
            if ($user->is_admin) {
                return true;
            }

            if (! app(PermissionRegistry::class)->has($ability)) {
                return null;
            }

            if (! $this->canUseTable('permissions')) {
                return false;
            }

            return app(UserHasPermissionQuery::class)->execute($user, $ability);
        });

        // View Composer para a Navbar (INSTITUIÇÃO)
        View::composer('layouts.master', function ($view) {
            $view->with('institution', $this->resolveInstitutionForLayout());
        });
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
