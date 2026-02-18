<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;

class Training extends Model
{
    use HasFactory;

    protected $table = 'trainings';

    protected $fillable = [
        'title',
        'description',
        'url',
        'is_active',
        'trainable_id',
        'trainable_type',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'url' => 'array',
    ];

    // Relacionamentos

    // Relação polimórfica: Retorna a instância de AssistiveTechnology ou AccessibleEducationalMaterial
    public function trainable(): MorphTo
    {
        return $this->morphTo();
    }

    // Arquivos do treinamento (PDF, DOC, etc.)
    public function files(): HasMany
    {
        return $this->hasMany(TrainingFile::class, 'training_id');
    }

    // Scopes

    public function scopeSearchTitle(Builder $query, ?string $title): Builder
    {
        if ($title) {
            return $query->where('title', 'like', "%{$title}%");
        }
        return $query;
    }

    public function scopeActive(Builder $query, bool $active = true): Builder
    {
        return $query->where('is_active', $active);
    }
}
