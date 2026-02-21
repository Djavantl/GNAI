<?php

namespace App\Models\InclusiveRadar;

use App\Enums\InclusiveRadar\BarrierStatus;
use App\Enums\Priority;
use App\Models\SpecializedEducationalSupport\Deficiency;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Barrier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'registered_by_user_id',
        'institution_id',
        'barrier_category_id',
        'location_id',
        'affected_student_id',
        'affected_professional_id',
        'not_applicable',
        'affected_person_name',
        'affected_person_role',
        'is_anonymous',
        'priority',
        'identified_at',
        'resolved_at',
        'is_active',
        'latitude',
        'longitude',
        'location_specific_details',
    ];

    protected $casts = [
        'identified_at' => 'date',
        'resolved_at' => 'date',
        'is_active' => 'boolean',
        'is_anonymous' => 'boolean',
        'not_applicable' => 'boolean',
        'priority' => Priority::class,
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function getReporterDisplayNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Contribuidor Anônimo';
        }

        return $this->registeredBy?->name ?? 'Usuário não identificado';
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by_user_id');
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class)->withTrashed();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BarrierCategory::class, 'barrier_category_id');
    }

    public function location() {
        return $this->belongsTo(Location::class)->withTrashed();
    }

    public function affectedStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'affected_student_id');
    }

    public function affectedProfessional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'affected_professional_id');
    }

    public function deficiencies(): BelongsToMany
    {
        return $this->belongsToMany(
            Deficiency::class,
            'barrier_deficiency',
            'barrier_id',
            'deficiency_id'
        )->withTimestamps();
    }

    public function latestStatus(): ?BarrierStatus
    {
        $status = $this->inspections()
            ->latest('inspection_date')
            ->latest('created_at')
            ->value('status');

        return $status;
    }

    public function inspections(): MorphMany
    {
        return $this->morphMany(Inspection::class, 'inspectable')
            ->with('images')
            ->orderByDesc('inspection_date')
            ->orderByDesc('created_at');
    }

    public function allImages(): HasManyThrough
    {
        return $this->hasManyThrough(
            InspectionImage::class,
            Inspection::class,
            'inspectable_id',
            'inspection_id',
            'id',
            'id'
        )->where('inspectable_type', static::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes para Filtros
    |--------------------------------------------------------------------------
    */

    public function scopeName(Builder $query, ?string $value): Builder
    {
        return $query->when($value, function (Builder $q) use ($value) {
            $q->where('name', 'like', "%{$value}%");
        });
    }

    public function scopeCategory(Builder $query, ?string $value): Builder
    {
        return $query->when($value, function (Builder $q) use ($value) {
            $q->whereHas('category', function (Builder $sub) use ($value) {
                $sub->where('name', 'like', "%{$value}%");
            });
        });
    }

    public function scopePriority(Builder $query, ?string $value): Builder
    {
        return $query->when($value, function (Builder $q) use ($value) {
            $q->where('priority', $value);
        });
    }

    public function scopeStatus(Builder $query, ?string $value): Builder
    {
        return $query->when($value, function (Builder $q) use ($value) {
            $q->whereHas('inspections', function (Builder $sub) use ($value) {
                $sub->where('status', $value);
            });
        });
    }
}
