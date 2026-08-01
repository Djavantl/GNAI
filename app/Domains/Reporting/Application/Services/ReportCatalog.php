<?php

declare(strict_types=1);

namespace App\Domains\Reporting\Application\Services;

use App\Domains\Reporting\Application\Contracts\ReportSource;
use App\Domains\Reporting\Domain\Exceptions\ReportingException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;

final class ReportCatalog
{
    /** @var array<string, ReportSource>|null */
    private ?array $sources = null;

    public function __construct(private readonly Container $container) {}

    /** @return Collection<int, ReportSource> */
    public function all(): Collection
    {
        return collect($this->sources())
            ->sortBy(fn (ReportSource $source): string => $source->label())
            ->values();
    }

    public function get(string $key): ReportSource
    {
        return $this->sources()[$key]
            ?? throw new ReportingException('A fonte de relatório selecionada não existe.');
    }

    public function forModel(string $model): ?ReportSource
    {
        return $this->all()->first(
            static fn (ReportSource $source): bool => $source->modelClass() === $model,
        );
    }

    /** @return array<string, ReportSource> */
    private function sources(): array
    {
        if ($this->sources !== null) {
            return $this->sources;
        }

        $this->sources = [];
        $domainsPath = app_path('Domains');

        if (! is_dir($domainsPath)) {
            return $this->sources;
        }

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($domainsPath));

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $path = $file->getPathname();

            if (! $file->isFile()
                || ! str_ends_with($file->getFilename(), 'ReportSource.php')
                || ! str_contains($path, DIRECTORY_SEPARATOR.'Infrastructure'.DIRECTORY_SEPARATOR.'Reporting'.DIRECTORY_SEPARATOR)) {
                continue;
            }

            $relative = substr($path, strlen(app_path()) + 1, -4);
            $class = 'App\\'.str_replace(DIRECTORY_SEPARATOR, '\\', $relative);

            if (! class_exists($class)
                || ! is_subclass_of($class, ReportSource::class)
                || (new ReflectionClass($class))->isAbstract()) {
                continue;
            }

            /** @var ReportSource $source */
            $source = $this->container->make($class);

            if (isset($this->sources[$source->key()])) {
                throw new ReportingException("Há mais de uma fonte de relatório com a chave {$source->key()}.");
            }

            $this->sources[$source->key()] = $source;
        }

        return $this->sources;
    }
}
