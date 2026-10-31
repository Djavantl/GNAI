<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use App\Domains\Audit\Domain\Contracts\Auditable;
use App\Domains\Audit\Domain\ValueObjects\AuditedField;
use App\Domains\Audit\Infrastructure\Eloquent\AuditsChanges;
use App\Domains\Inclusion\Domain\DTOs\Tags\CreateTagDTO;
use App\Domains\Inclusion\Domain\DTOs\Tags\UpdateTagDTO;
use Database\Factories\Domains\Inclusion\TagFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[UseFactory(TagFactory::class)]
final class Tag extends Model implements Auditable
{
    use AuditsChanges;
    use HasFactory;

    protected $table = 'tags';

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function auditType(): string
    {
        return 'tag';
    }

    public static function auditLabel(): string
    {
        return 'Etiqueta';
    }

    public static function auditedFields(): array
    {
        return [
            'name' => AuditedField::text('Nome da Etiqueta'),
            'is_active' => AuditedField::boolean('Etiqueta Ativa'),
        ];
    }

    public function auditDescription(): string
    {
        return "a etiqueta {$this->name}";
    }

    public static function register(CreateTagDTO $data): self
    {
        return new self([
            'name' => self::normalizeName($data->name),
            'is_active' => $data->isActive,
        ]);
    }

    public function revise(UpdateTagDTO $data): void
    {
        $this->fill([
            'name' => self::normalizeName($data->name),
            'is_active' => $data->isActive,
        ]);
    }

    public function publications(): BelongsToMany
    {
        return $this->belongsToMany(
            Publication::class,
            'publication_tag',
            'tag_id',
            'publication_id',
        );
    }

    private static function normalizeName(string $name): string
    {
        return trim($name);
    }
}
