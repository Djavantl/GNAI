<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;

class PeiAdaptation extends Model
{
    protected $fillable = [
        'pei_id',
        'course_subject', 
        'teacher_name', 
        'specific_objectives', 
        'content_programmatic', 
        'methodology_strategies',
    ];

    public function pei()
    {
        return $this->belongsTo(Pei::class);
    }

    public function evaluation()
    {
        return $this->hasOne(PeiEvaluation::class);
    }
}
