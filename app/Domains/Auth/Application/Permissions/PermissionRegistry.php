<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Permissions;

use App\Domains\Auth\Domain\DTOs\Permissions\PermissionDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use Throwable;

final class PermissionRegistry
{
    private const MANIFEST_PATH = 'bootstrap/cache/permissions.php';

    /**
     * @var Collection<int, PermissionDTO>|null
     */
    private ?Collection $manifestPermissions = null;

    /**
     * Diretórios onde permissões podem aparecer em código de aplicação.
     *
     * @var list<string>
     */
    private const SCAN_DIRECTORIES = [
        'app',
        'routes',
        'resources/views',
    ];

    /**
     * @return Collection<int, PermissionDTO>
     */
    public function all(): Collection
    {
        return $this->manifestPermissions();
    }

    public function has(string $slug): bool
    {
        return $this->slugs()->contains($slug);
    }

    /**
     * @return Collection<int, string>
     */
    public function slugs(): Collection
    {
        return $this->manifestPermissions()
            ->map(fn (PermissionDTO $permission): string => $permission->slug)
            ->values();
    }

    /**
     * Escaneia o filesystem. Deve ser usado por comandos de sincronização, não
     * pelo fluxo de autorização em tempo de execução.
     *
     * @return Collection<int, PermissionDTO>
     */
    public function discover(): Collection
    {
        return $this->discoverSlugs()
            ->map(fn (string $slug): PermissionDTO => new PermissionDTO(
                name: $this->humanize($slug),
                slug: $slug,
            ))
            ->values();
    }

    /**
     * @param Collection<int, PermissionDTO> $permissions
     */
    public function writeManifest(Collection $permissions): void
    {
        $path = base_path(self::MANIFEST_PATH);
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $payload = [
            'generated_at' => now()->toIso8601String(),
            'permissions' => $permissions
                ->sortBy(fn (PermissionDTO $permission): string => $permission->slug)
                ->map(fn (PermissionDTO $permission): array => $permission->toArray())
                ->values()
                ->all(),
        ];

        file_put_contents(
            $path,
            "<?php\n\nreturn ".var_export($payload, true).";\n"
        );

        $this->manifestPermissions = null;
    }

    /**
     * @return Collection<int, PermissionDTO>
     */
    private function manifestPermissions(): Collection
    {
        if ($this->manifestPermissions instanceof Collection) {
            return $this->manifestPermissions;
        }

        $path = base_path(self::MANIFEST_PATH);

        if (is_file($path)) {
            $manifest = require $path;
            $permissions = $manifest['permissions'] ?? [];

            return $this->manifestPermissions = collect(is_array($permissions) ? $permissions : [])
                ->filter(fn ($permission): bool => is_array($permission)
                    && isset($permission['slug'], $permission['name'])
                    && is_string($permission['slug'])
                    && is_string($permission['name'])
                    && $this->looksLikePermissionSlug($permission['slug']))
                ->map(fn (array $permission): PermissionDTO => PermissionDTO::fromArray($permission))
                ->sortBy(fn (PermissionDTO $permission): string => $permission->slug)
                ->values();
        }

        return $this->manifestPermissions = $this->databasePermissions();
    }

    /**
     * Fallback barato quando o manifest ainda não foi gerado, útil em ambientes
     * recém-provisionados. Não escaneia código em tempo de execução.
     *
     * @return Collection<int, PermissionDTO>
     */
    private function databasePermissions(): Collection
    {
        try {
            if (! Schema::hasTable('permissions')) {
                return collect();
            }

            return DB::table('permissions')
                ->select(['name', 'slug'])
                ->orderBy('slug')
                ->get()
                ->map(fn (object $permission): PermissionDTO => new PermissionDTO(
                    name: (string) $permission->name,
                    slug: (string) $permission->slug,
                ))
                ->filter(fn (PermissionDTO $permission): bool => $this->looksLikePermissionSlug($permission->slug))
                ->values();
        } catch (Throwable) {
            return collect();
        }
    }

    /**
     * @return Collection<int, string>
     */
    private function discoverSlugs(): Collection
    {
        static $slugs = null;

        if ($slugs instanceof Collection) {
            return $slugs;
        }

        $found = collect();

        foreach (self::SCAN_DIRECTORIES as $directory) {
            $path = base_path($directory);

            if (! is_dir($path)) {
                continue;
            }

            foreach ($this->files($path) as $file) {
                $contents = file_get_contents($file->getPathname());

                if ($contents === false) {
                    continue;
                }

                $found = $found->merge($this->extractSlugs($contents));
            }
        }

        return $slugs = $found
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * @return iterable<SplFileInfo>
     */
    private function files(string $path): iterable
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if (! $file instanceof SplFileInfo || ! $file->isFile()) {
                continue;
            }

            if (! in_array($file->getExtension(), ['php'], true)) {
                continue;
            }

            yield $file;
        }
    }

    /**
     * @return Collection<int, string>
     */
    private function extractSlugs(string $contents): Collection
    {
        $patterns = [
            "/middleware\\(\\s*['\"]can:([^'\",]+)(?:,[^'\"]*)?['\"]\\s*\\)/",
            "/@can\\(\\s*['\"]([^'\"]+)['\"]/",
            "/->can\\(\\s*['\"]([^'\"]+)['\"]/",
            "/\\bcan\\(\\s*['\"]([^'\"]+)['\"]/",
        ];

        $slugs = collect();

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $contents, $matches)) {
                $slugs = $slugs->merge($matches[1]);
            }
        }

        return $slugs
            ->map(fn (string $slug): string => trim(strtok($slug, ',') ?: $slug))
            ->filter(fn (string $slug): bool => $this->looksLikePermissionSlug($slug));
    }

    private function looksLikePermissionSlug(string $slug): bool
    {
        return preg_match('/^[a-z0-9][a-z0-9._-]*\\.[a-z0-9][a-z0-9._-]*$/', $slug) === 1;
    }

    private function humanize(string $slug): string
    {
        [$entity, $action] = array_pad(explode('.', $slug, 2), 2, '');

        $actionLabel = match ($action) {
            'index', 'view' => 'Visualizar',
            'view-all' => 'Visualizar todos',
            'view-own' => 'Visualizar próprios',
            'create' => 'Criar',
            'store' => 'Salvar',
            'show' => 'Visualizar',
            'edit', 'update' => 'Editar',
            'delete', 'destroy' => 'Excluir',
            'pdf' => 'Gerar PDF',
            'inspection.show' => 'Visualizar vistoria de',
            'download' => 'Baixar',
            'restore' => 'Restaurar',
            'upload' => 'Enviar',
            'return' => 'Registrar devolução',
            'cancel' => 'Cancelar',
            'available' => 'Listar disponíveis',
            'meta' => 'Ver metadados',
            'run' => 'Executar',
            'builder' => 'Usar construtor',
            default => str($action)->replace(['.', '-', '_'], ' ')->title()->toString(),
        };

        $entityTranslationKey = "permissions.entities.{$entity}";
        $entityTranslation = __($entityTranslationKey);

        $entityLabel = $entityTranslation !== $entityTranslationKey
            ? $entityTranslation
            : str($entity)->replace(['.', '-', '_'], ' ')->title()->toString();

        return trim("{$actionLabel} {$entityLabel}");
    }
}
