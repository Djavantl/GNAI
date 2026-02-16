<?php

namespace App\Models\SpecializedEducationalSupport;
use App\Enums\SpecializedEducationalSupport\EvaluationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\SpecializedEducationalSupport\Pei;

class PeiEvaluation extends Model
{
    protected $fillable = [
        'pei_id',
        'semester_id',
        'evaluation_instruments',
        'parecer',
        'successful_proposals',
        'next_stage_goals',
        'evaluation_type',
        'evaluation_date',
        'evaluated_by_professional_id',
    ];

    protected $casts = [
        'evaluation_type' => EvaluationType::class,
        'evaluation_date' => 'date',
    ];

    public function pei(): BelongsTo
    {
        return $this->belongsTo(Pei::class, 'pei_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'evaluated_by_professional_id');
    }
}
