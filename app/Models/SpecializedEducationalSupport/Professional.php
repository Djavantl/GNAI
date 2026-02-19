<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Traits\GlobalSearchable;

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
}
