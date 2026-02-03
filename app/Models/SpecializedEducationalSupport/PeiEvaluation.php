<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;

class PeiEvaluation extends Model
{
    protected $fillable = [
        'pei_adaptation_id', 
        'evaluation_instruments', 
        'final_parecer', 
        'successful_proposals', 
        'next_stage_goals',
    ];

    public function adaptation()
    {
        return $this->belongsTo(PeiAdaptation::class, 'pei_adaptation_id');
    }
}
