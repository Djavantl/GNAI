<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Auth\Domain\Models\User;
use App\Domains\Inclusion\Domain\DTOs\Publications\CreatePublicationDTO;
use App\Domains\Inclusion\Domain\DTOs\Publications\UpdatePublicationDTO;
use App\Domains\Inclusion\Domain\Enums\PublicationType;
use App\Domains\Inclusion\Domain\Exceptions\InvalidPublication;
use App\Domains\Inclusion\Domain\Models\Professional;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Database\Factories\Domains\Inclusion\PublicationFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Publicação institucional. Originalmente a agenda institucional, passou a
 * representar notícias, comunicados, artigos, registros e eventos exibidos
 * no site. Eventos mantêm período (has_schedule), exibido como registro no site.
 */
#[UseFactory(PublicationFactory::class)]
class Publication extends Model implements Auditable
{
    use AuditsChanges;
    use HasFactory;

    public const SLUG_MAX_LENGTH = 160;

    protected $table = 'publications';

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'type',
        'category',
        'has_schedule',
        'description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'location',
        'organizer',
        'audience',
        'author_id',
        'link_url',
        'link_label',
        'cover_path',
        'cover_alt',
        'is_active',
        'published_at',
        'is_featured',
        'seo_title',
        'seo_description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'is_active' => 'boolean',
        'has_schedule' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'type' => PublicationType::class,
    ];

    protected $attributes = [
        'type' => 'event',
        'has_schedule' => true,
        'is_featured' => false,
    ];

    protected static function booted(): void
    {
        static::saving(function (self $publication): void {
            if (blank($publication->slug)) {
                $publication->slug = self::uniqueSlug($publication->title, $publication->getKey());
            }

            if ($publication->is_active && $publication->published_at === null) {
                $publication->published_at = now();
            }
        });
    }

    public static function auditType(): string
    {
        return 'publication';
    }

    public static function auditLabel(): string
    {
        return 'Publicação';
    }

    public static function auditedFields(): array
    {
        return [
            'title' => AuditedField::text('Título'),
            'slug' => AuditedField::text('Endereço da página'),
            'summary' => AuditedField::text('Resumo'),
            'type' => AuditedField::enum('Tipo', PublicationType::class),
            'category' => AuditedField::text('Categoria'),
            'has_schedule' => AuditedField::boolean('Possui datas'),
            'description' => AuditedField::richText('Conteúdo'),
            'start_date' => AuditedField::date('Data de Início'),
            'end_date' => AuditedField::date('Data de Término'),
            'start_time' => AuditedField::time('Horário de Início'),
            'end_time' => AuditedField::time('Horário de Término'),
            'location' => AuditedField::text('Local'),
            'organizer' => AuditedField::text('Organizador'),
            'audience' => AuditedField::text('Público'),
            'author_id' => AuditedField::reference('Autor', User::class),
            'link_url' => AuditedField::text('Link'),
            'link_label' => AuditedField::text('Texto do link'),
            'cover_path' => AuditedField::file('Capa'),
            'cover_alt' => AuditedField::text('Texto alternativo da capa'),
            'is_active' => AuditedField::boolean('Publicado'),
            'published_at' => AuditedField::date('Data de publicação'),
            'is_featured' => AuditedField::boolean('Destaque'),
            'seo_title' => AuditedField::text('Título para buscadores'),
            'seo_description' => AuditedField::text('Descrição para buscadores'),
        ];
    }

    public function auditDescription(): string
    {
        return "a publicação {$this->title}";
    }

    /**
     * @throws InvalidPublication
     */
    public static function schedule(CreatePublicationDTO $data): self
    {
        return new self(self::attributesFrom($data));
    }

    /**
     * @throws InvalidPublication
     */
    public function revise(UpdatePublicationDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(PublicationMedia::class, 'publication_id')
            ->orderBy('position')
            ->orderBy('id');
    }

    public function speakers(): BelongsToMany
    {
        return $this->belongsToMany(
            Professional::class,
            'publication_speakers',
            'publication_id',
            'professional_id',
        )
            ->withPivot('position')
            ->orderByPivot('position');
    }

    /**
     * Publicações visíveis no site: publicadas e com data de publicação alcançada.
     */
    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function isPubliclyVisible(): bool
    {
        return $this->is_active
            && ($this->published_at === null || $this->published_at->lte(now()));
    }

    public function startsAt(): ?CarbonInterface
    {
        if (! $this->has_schedule || $this->start_date === null) {
            return null;
        }

        return $this->start_date->copy()->setTimeFrom($this->start_time ?? $this->start_date->copy()->startOfDay());
    }

    public function endsAt(): ?CarbonInterface
    {
        if (! $this->has_schedule || $this->start_date === null) {
            return null;
        }

        $date = ($this->end_date ?? $this->start_date)->copy();

        return $this->end_time
            ? $date->setTimeFrom($this->end_time)
            : $date->endOfDay();
    }

    /**
     * Situação temporal do evento: upcoming, ongoing, finished ou null quando não há datas.
     */
    public function scheduleStatus(): ?string
    {
        $startsAt = $this->startsAt();
        $endsAt = $this->endsAt();

        if ($startsAt === null || $endsAt === null) {
            return null;
        }

        return match (true) {
            now()->lt($startsAt) => 'upcoming',
            now()->lte($endsAt) => 'ongoing',
            default => 'finished',
        };
    }

    public static function uniqueSlug(string $source, int|string|null $ignoreId = null): string
    {
        $base = Str::limit(Str::slug($source), self::SLUG_MAX_LENGTH, '');
        $base = trim($base, '-') !== '' ? trim($base, '-') : 'publicacao';
        $slug = $base;
        $suffix = 2;

        while (
            self::query()
                ->where('slug', $slug)
                ->when($ignoreId !== null, fn (Builder $query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    /**
     * @throws InvalidPublication
     */
    private static function attributesFrom(CreatePublicationDTO|UpdatePublicationDTO $data): array
    {
        $type = PublicationType::tryFrom($data->type) ?? PublicationType::EVENT;
        $hasSchedule = $data->hasSchedule;

        $attributes = [
            'title' => self::normalizeText($data->title),
            'slug' => self::normalizeSlug($data->slug),
            'summary' => self::normalizeNullableText($data->summary),
            'type' => $type->value,
            'category' => self::normalizeNullableText($data->category),
            'has_schedule' => $hasSchedule,
            'description' => self::normalizeNullableText($data->description),
            'location' => self::normalizeNullableText($data->location),
            'organizer' => self::normalizeNullableText($data->organizer),
            'audience' => self::normalizeNullableText($data->audience),
            'author_id' => $data->authorId,
            'link_url' => self::normalizeNullableText($data->linkUrl),
            'link_label' => self::normalizeNullableText($data->linkLabel),
            'cover_alt' => self::normalizeNullableText($data->coverAlt),
            'is_active' => $data->isActive,
            'published_at' => filled($data->publishedAt) ? Carbon::parse($data->publishedAt) : null,
            'is_featured' => $data->isFeatured,
            'seo_title' => self::normalizeNullableText($data->seoTitle),
            'seo_description' => self::normalizeNullableText($data->seoDescription),
            'start_date' => null,
            'end_date' => null,
            'start_time' => null,
            'end_time' => null,
        ];

        if (! $hasSchedule) {
            return $attributes;
        }

        if (blank($data->startDate) || blank($data->endDate) || blank($data->startTime) || blank($data->endTime)) {
            throw new InvalidPublication('Informe as datas e os horários de início e término do evento.');
        }

        self::ensureValidPeriod(
            startDate: $data->startDate,
            endDate: $data->endDate,
            startTime: $data->startTime,
            endTime: $data->endTime,
        );

        return [
            ...$attributes,
            'start_date' => Carbon::parse($data->startDate)->toDateString(),
            'end_date' => Carbon::parse($data->endDate)->toDateString(),
            'start_time' => Carbon::createFromFormat('H:i', self::normalizeTime($data->startTime))->format('H:i'),
            'end_time' => Carbon::createFromFormat('H:i', self::normalizeTime($data->endTime))->format('H:i'),
        ];
    }

    /**
     * @throws InvalidPublication
     */
    private static function ensureValidPeriod(string $startDate, string $endDate, string $startTime, string $endTime): void
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $startsAt = Carbon::createFromFormat('H:i', self::normalizeTime($startTime));
        $endsAt = Carbon::createFromFormat('H:i', self::normalizeTime($endTime));

        if ($end->lt($start)) {
            throw new InvalidPublication('A data de término não pode ser anterior à data de início.');
        }

        if ($start->eq($end) && $endsAt->lte($startsAt)) {
            throw new InvalidPublication('O horário de término deve ser maior que o horário de início para o mesmo dia.');
        }
    }

    private static function normalizeText(string $value): string
    {
        return trim($value);
    }

    private static function normalizeNullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private static function normalizeSlug(?string $value): ?string
    {
        $slug = Str::limit(Str::slug((string) $value), self::SLUG_MAX_LENGTH, '');
        $slug = trim($slug, '-');

        return $slug === '' ? null : $slug;
    }

    private static function normalizeTime(string $time): string
    {
        return substr($time, 0, 5);
    }
}
