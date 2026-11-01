<?php

declare(strict_types=1);

namespace App\Domains\Website\Application\Actions;

use App\Domains\Website\Application\Data\UpdateWebsiteData;
use App\Domains\Website\Domain\DTOs\UpdateWebsiteDTO;
use App\Domains\Website\Domain\Enums\WebsiteSectionType;
use App\Domains\Website\Domain\Exceptions\InvalidWebsite;
use App\Domains\Website\Domain\Models\WebsiteSetting;
use App\Domains\Website\Infrastructure\Storage\WebsiteMediaStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Salva identidade e seções da página inicial. Imagens já existentes só são
 * mantidas quando pertencem à mesma seção salva anteriormente; o caminho
 * enviado pelo navegador nunca é aceito como referência a outro arquivo.
 */
final readonly class UpdateWebsiteAction
{
    public function __construct(private WebsiteMediaStorage $storage) {}

    /**
     * @throws InvalidWebsite
     * @throws Throwable
     */
    public function execute(UpdateWebsiteData $data): WebsiteSetting
    {
        $setting = WebsiteSetting::query()->oldest('id')->first() ?? new WebsiteSetting;
        $previousSections = collect($setting->sections ?? [])
            ->filter(fn ($section): bool => is_array($section) && isset($section['id']))
            ->keyBy('id');
        $previousLogo = $setting->logo_path;
        $storedPaths = [];

        try {
            $logoPath = $previousLogo;

            if ($data->logo) {
                $logoPath = $this->storage->store($data->logo);
                $storedPaths[] = $logoPath;
            } elseif ($data->removeLogo) {
                $logoPath = null;
            }

            $sections = [];

            foreach ($data->sections as $input) {
                if (! is_array($input)) {
                    continue;
                }

                $section = WebsiteSetting::normalizeSection($input, $previousSections->get((string) ($input['id'] ?? '')));

                if ($section === null) {
                    continue;
                }

                $image = $input['image'] ?? null;

                if ($image instanceof UploadedFile && WebsiteSectionType::from($section['type'])->hasImage()) {
                    $section['image_path'] = $this->storage->store($image);
                    $storedPaths[] = $section['image_path'];
                }

                if ($section['type'] === WebsiteSectionType::HERO->value) {
                    $previous = $previousSections->get($section['id']);
                    $previousSlides = ($previous['type'] ?? null) === 'hero'
                        ? (array) ($previous['slides'] ?? []) : [];

                    $section['slides'] = $this->storage->storeSlideFiles(
                        (array) ($input['slides'] ?? []),
                        $previousSlides,
                    );
                    $storedPaths = [
                        ...$storedPaths,
                        ...array_diff(
                            array_column($section['slides'], 'image_path'),
                            array_column($previousSlides, 'image_path'),
                        ),
                    ];
                }

                WebsiteSetting::ensureSectionImageDescribed($section);

                $sections[] = $section;
            }

            $websiteDTO = new UpdateWebsiteDTO(
                siteName: $data->siteName,
                primaryColor: $data->primaryColor,
                tagline: $data->tagline,
                logoPath: $logoPath,
                contactEmail: $data->contactEmail,
                contactPhone: $data->contactPhone,
                address: $data->address,
                instagramUrl: $data->instagramUrl,
                facebookUrl: $data->facebookUrl,
                youtubeUrl: $data->youtubeUrl,
                websiteUrl: $data->websiteUrl,
                footerText: $data->footerText,
                sections: $sections,
            );

            DB::transaction(function () use ($setting, $websiteDTO): void {
                $setting->revise($websiteDTO);
                $setting->save();
            });
        } catch (Throwable $exception) {
            $this->storage->deleteMany($storedPaths);

            throw $exception;
        }

        $keptPaths = $this->storage->pathsForSections($sections);
        $keptPaths[] = $logoPath;
        $previousPaths = collect([...$this->storage->pathsForSections($previousSections->all()), $previousLogo])->filter();

        $this->storage->deleteMany($previousPaths->reject(fn (string $path): bool => in_array($path, $keptPaths, true)));

        return $setting;
    }
}
