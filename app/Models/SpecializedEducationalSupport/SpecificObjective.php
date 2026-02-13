<?php

namespace App\Models\SpecializedEducationalSupport;

use App\Enums\SpecializedEducationalSupport\ObjectiveStatus;
use Illuminate\Database\Eloquent\Model;

class SpecificObjective extends Model
{
    protected $fillable = [
        'pei_id', 
        'description', 
        'status', 
        'observations_progress'
    ];

    protected $casts = [
        'status' => ObjectiveStatus::class,
    ];

    public function pei() { 
        return $this->belongsTo(Pei::class); 
    }
}