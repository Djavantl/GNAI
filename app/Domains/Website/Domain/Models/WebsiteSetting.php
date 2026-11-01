<?php

declare(strict_types=1);

namespace App\Domains\Website\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Website\Domain\DTOs\UpdateWebsiteDTO;
use App\Domains\Website\Domain\Enums\WebsiteSectionType;
use App\Domains\Website\Domain\Exceptions\InvalidWebsite;
use App\Domains\Website\Domain\Support\WebsiteDefaults;
use App\Domains\Website\Domain\Support\AboutAppearance;
use App\Domains\Website\Domain\Support\GuidanceAppearance;
use App\Domains\Website\Domain\Support\TeamAppearance;
use App\Domains\Website\Domain\Support\ContactAppearance;
use App\Domains\Website\Domain\Support\PublicationsAppearance;
use App\Domains\Website\Domain\Support\PillarCardAppearance;
use App\Domains\Website\Domain\Support\PillarsAppearance;
use App\Domains\Website\Domain\Support\HeroAppearance;
use App\Shared\Infrastructure\Security\RichTextSanitizer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

/**
 * Configuração única do site white label: identidade, contatos e seções
 * da página inicial montadas pelo construtor.
 */
final class WebsiteSetting extends Model implements Auditable
{
    use AuditsChanges;

    protected $table = 'website_settings';

    protected $fillable = [
        'site_name',
        'tagline',
        'logo_path',
        'primary_color',
        'contact_email',
        'contact_phone',
        'address',
        'social_links',
        'footer_text',
        'sections',
    ];

    protected $casts = [
        'social_links' => 'array',
        'sections' => 'array',
    ];

    public static function auditType(): string
    {
        return 'website_setting';
    }

    public static function auditLabel(): string
    {
        return 'Site';
    }

    public static function auditedFields(): array
    {
        return [
            'site_name' => AuditedField::text('Nome do site'),
            'tagline' => AuditedField::text('Slogan'),
            'logo_path' => AuditedField::file('Logo'),
            'primary_color' => AuditedField::text('Cor principal'),
            'contact_email' => AuditedField::text('E-mail de contato'),
            'contact_phone' => AuditedField::text('Telefone de contato'),
            'address' => AuditedField::text('Endereço'),
            'footer_text' => AuditedField::text('Texto do rodapé'),
        ];
    }

    public function auditDescription(): string
    {
        return 'a configuração do site';
    }

    /**
     * Retorna a configuração salva ou uma instância não persistida com os padrões.
     */
    public static function current(): self
    {
        try {
            $setting = self::query()->oldest('id')->first();
        } catch (QueryException) {
            $setting = null;
        }

        return $setting ?? new self([
            ...WebsiteDefaults::identity(),
            'sections' => WebsiteDefaults::sections(),
        ]);
    }

    public function revise(UpdateWebsiteDTO $data): void
    {
        $this->fill([
            'site_name' => trim($data->siteName),
            'tagline' => self::normalizeNullableText($data->tagline),
            'logo_path' => $data->logoPath,
            'primary_color' => strtolower($data->primaryColor),
            'contact_email' => self::normalizeNullableText($data->contactEmail),
            'contact_phone' => self::normalizeNullableText($data->contactPhone),
            'address' => self::normalizeNullableText($data->address),
            'social_links' => [
                'instagram' => self::normalizeNullableText($data->instagramUrl),
                'facebook' => self::normalizeNullableText($data->facebookUrl),
                'youtube' => self::normalizeNullableText($data->youtubeUrl),
                'website' => self::normalizeNullableText($data->websiteUrl),
            ],
            'footer_text' => self::normalizeNullableText($data->footerText),
            'sections' => $data->sections,
        ]);
    }

    /**
     * Normaliza uma seção enviada pelo construtor. A imagem existente só é mantida
     * quando pertence à mesma seção salva anteriormente, com o mesmo tipo.
     *
     * @param  array<string, mixed>  $input
     * @param  array<string, mixed>|null  $previous
     * @return array<string, mixed>|null
     */
    public static function normalizeSection(array $input, ?array $previous): ?array
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
                    'url' => $type === WebsiteSectionType::RESOURCES ? trim((string) ($item['url'] ?? '')) : '',
                    'title' => trim((string) ($item['title'] ?? '')),
                    'text' => trim((string) ($item['text'] ?? '')),
                    ...(in_array($type->value, [...PillarsAppearance::SECTION_TYPES, 'guidance'], true) ? [
                        'appearance' => PillarCardAppearance::normalize((array) ($item['appearance'] ?? []), $type->value),
                    ] : []),
                ];

                if ($normalized['title'] !== '' || $normalized['text'] !== '' || $normalized['url'] !== '') {
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
            ...(in_array($type->value, PillarsAppearance::SECTION_TYPES, true) ? ['pillars' => PillarsAppearance::normalize((array) ($input['pillars'] ?? []), $type->value)] : []),
            ...($type === WebsiteSectionType::GUIDANCE ? ['guidance' => GuidanceAppearance::normalize((array) ($input['guidance'] ?? []))] : []),
            ...($type->value === 'publications' ? ['publications' => PublicationsAppearance::normalize((array) ($input['publications'] ?? []))] : []),
            ...($type->value === 'events' ? ['events' => PublicationsAppearance::normalize((array) ($input['events'] ?? []))] : []),
            ...($type->value === 'team' ? ['team' => TeamAppearance::normalize((array) ($input['team'] ?? []))] : []),
            ...($type->value === 'contact' ? ['contact' => ContactAppearance::normalize((array) ($input['contact'] ?? []))] : []),
            ...($type === WebsiteSectionType::ABOUT ? ['about' => AboutAppearance::normalize((array) ($input['about'] ?? []))] : []),
            ...($type === WebsiteSectionType::HERO ? ['hero' => HeroAppearance::normalize((array) ($input['hero'] ?? [])), 'slides' => []] : []),
        ]);
    }

    /**
     * @param  array<string, mixed>  $section
     *
     * @throws InvalidWebsite
     */
    public static function ensureSectionImageDescribed(array $section): void
    {
        if (
            $section['type'] === WebsiteSectionType::ABOUT->value
            && $section['image_path'] !== null
            && $section['image_alt'] === ''
        ) {
            throw new InvalidWebsite('Descreva a imagem da seção "Sobre nós" no texto alternativo.');
        }
    }

    private static function normalizeNullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
