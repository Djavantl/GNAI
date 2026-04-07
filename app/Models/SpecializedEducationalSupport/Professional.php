<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\Traits\Reportable;

class Professional extends Model
{   
    use Reportable;
    
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

    public static function getEmbeddedRelations(): array
    {
        return ['person'];
    }


    public static function getReportLabel(): string
    {
        return 'Profissionais';
    }

    public static function getReportColumns(): ?array
    {
        return ['person.name', 'registration', 'status', 'entry_date', 'person.email', 'person.document', 'person.birth_date', 'person.gender', 'person.phone', 'person.address'];
    }


    public static function getReportColumnLabels(): array
    {
        return [
            'registration' => 'Matrícula',
            'person.name'  => 'Nome do Profissional',
            'entry_date'   => 'Data de Ingresso',
            'person.email' => 'E-mail',
            'person.document'=> 'CPF',
            'person.birth_date'=> 'Data de Nascimento',
            'person.gender'=> 'Gênero',
            'person.phone'=> 'Telefone',
            'person.address'=> 'Endereço',
        ];
    }

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
