<?php

declare(strict_types=1);

namespace App\Domains\Website\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Website\Domain\Support\WebsiteDefaults;
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
}
