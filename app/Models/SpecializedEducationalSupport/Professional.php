<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\GlobalSearchable;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class Professional extends Model
{   
    use GlobalSearchable;
    
    protected $fillable = [
        'person_id',
        'position_id',
        'registration',
        'entry_date',
        'status',
    ];

    protected $searchable = [
        'person.document',
        'person.name',
        'person.email',
        'status',
    ];

    protected $searchAliases = [
        'ativo' => ['active'],
        'inativo' => ['inactive'],
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function scopeName(Builder $query, ?string $term): Builder
    {
        if (!$term) return $query;

        return $query->whereHas('person', fn($q) =>
            $q->where('name', 'like', "%{$term}%")
        );
    }

    public function scopeEmail(Builder $query, ?string $term): Builder
    {
        if (!$term) return $query;

        return $query->whereHas('person', fn($q) =>
            $q->where('email', 'like', "%{$term}%")
        );
    }

    public function scopePosition(Builder $query, $positionId): Builder
    {
        if (!is_null($positionId) && $positionId !== '') {
            $query->where('position_id', $positionId);
        }

        return $query;
    }

    public function scopeStatus(Builder $query, $status): Builder
    {
        if (!is_null($status) && $status !== '') {
            $query->where('status', $status);
        }

        return $query;
    }

    public function scopeSemester(Builder $query, $semesterId): Builder
    {
        if (!is_null($semesterId) && $semesterId !== '') {
            $query->whereHas('person', fn($q) =>
                $q->where('semester_id', $semesterId)
            );
        }

        return $query;
    }
}
