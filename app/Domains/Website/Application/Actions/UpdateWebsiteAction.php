<?php

declare(strict_types=1);

namespace App\Domains\Website\Application\Actions;

use App\Domains\Website\Application\Data\UpdateWebsiteData;
use App\Domains\Website\Domain\Enums\WebsiteSectionType;
use App\Domains\Website\Domain\Exceptions\InvalidWebsite;
use App\Domains\Website\Domain\Models\WebsiteSetting;
use App\Domains\Website\Domain\Support\WebsiteDefaults;
use App\Domains\Website\Infrastructure\Storage\WebsiteMediaStorage;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
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

                $section = $this->normalizeSection($input, $previousSections->get((string) ($input['id'] ?? '')));

                if ($section === null) {
                    continue;
                }

                $image = $input['image'] ?? null;

                if ($image instanceof UploadedFile && WebsiteSectionType::from($section['type'])->hasImage()) {
                    $section['image_path'] = $this->storage->store($image);
                    $storedPaths[] = $section['image_path'];
                }

                if (
                    $section['type'] === WebsiteSectionType::ABOUT->value
                    && $section['image_path'] !== null
                    && $section['image_alt'] === ''
                ) {
                    throw new InvalidWebsite('Descreva a imagem da seção "Sobre nós" no texto alternativo.');
                }

                $sections[] = $section;
            }

            DB::transaction(function () use ($setting, $data, $logoPath, $sections): void {
                $setting->fill([
                    'site_name' => trim($data->siteName),
                    'tagline' => $this->nullable($data->tagline),
                    'logo_path' => $logoPath,
                    'primary_color' => strtolower($data->primaryColor),
                    'contact_email' => $this->nullable($data->contactEmail),
                    'contact_phone' => $this->nullable($data->contactPhone),
                    'address' => $this->nullable($data->address),
                    'social_links' => [
                        'instagram' => $this->nullable($data->instagramUrl),
                        'facebook' => $this->nullable($data->facebookUrl),
                        'youtube' => $this->nullable($data->youtubeUrl),
                        'website' => $this->nullable($data->websiteUrl),
                    ],
                    'footer_text' => $this->nullable($data->footerText),
                    'sections' => $sections,
                ]);
                $setting->save();
            });
        } catch (Throwable $exception) {
            $this->storage->deleteMany($storedPaths);

            throw $exception;
        }

        $keptPaths = collect($sections)->pluck('image_path')->filter()->push($logoPath)->filter()->all();
        $previousPaths = $previousSections->pluck('image_path')->push($previousLogo)->filter();

        $this->storage->deleteMany($previousPaths->reject(fn (string $path): bool => in_array($path, $keptPaths, true)));

        return $setting;
    }

    /**
     * @param  array<string, mixed>  $input
     * @param  array<string, mixed>|null  $previous
     * @return array<string, mixed>|null
     */
    private function normalizeSection(array $input, ?array $previous): ?array
    {
        $type = WebsiteSectionType::tryFrom((string) ($input['type'] ?? ''));

        if ($type === null) {
            return null;
        }

        $imagePath = null;

        if (
            $type->hasImage()
            && ! filter_var($input['remove_image'] ?? false, FILTER_VALIDATE_BOOL)
            && $previous !== null
            && ($previous['type'] ?? null) === $type->value
        ) {
            $imagePath = $previous['image_path'] ?? null;
        }

        $items = [];

        if ($type->hasItems()) {
            foreach (array_values((array) ($input['items'] ?? [])) as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $normalized = [
                    'icon' => trim((string) ($item['icon'] ?? '')),
                    'value' => trim((string) ($item['value'] ?? '')),
                    'title' => trim((string) ($item['title'] ?? '')),
                    'text' => trim((string) ($item['text'] ?? '')),
                ];

                if (implode('', $normalized) !== '') {
                    $items[] = $normalized;
                }
            }
        }

        $body = trim((string) ($input['body'] ?? ''));

        return WebsiteDefaults::section((string) $input['id'], $type->value, [
            'enabled' => filter_var($input['enabled'] ?? true, FILTER_VALIDATE_BOOL),
            'title' => trim((string) ($input['title'] ?? '')),
            'subtitle' => trim((string) ($input['subtitle'] ?? '')),
            'body' => $type->hasRichBody() && $body !== '' ? RichTextSanitizer::sanitize($body) : '',
            'button_label' => trim((string) ($input['button_label'] ?? '')),
            'button_url' => trim((string) ($input['button_url'] ?? '')),
            'image_path' => $imagePath,
            'image_alt' => trim((string) ($input['image_alt'] ?? '')),
            'limit' => max(1, min(12, (int) ($input['limit'] ?? 3))),
            'items' => $items,
        ]);
    }

    private function nullable(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
