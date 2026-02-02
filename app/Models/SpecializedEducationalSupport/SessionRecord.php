<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionRecord extends Model
{
     use SoftDeletes;

    protected $fillable = [
        'attendance_sessions_id',
        'record_date',
        'duration',
        'activities_performed',
        'strategies_used',
        'resources_used',
        'adaptations_made',
        'student_participation',
        'engagement_level',
        'observed_behavior',
        'response_to_activities',
        'development_evaluation',
        'progress_indicators',
        'recommendations',
        'next_session_adjustments',
        'external_referral_needed',
        'general_observations',
    ];

    protected $casts = [
        'record_date' => 'date',
        'external_referral_needed' => 'boolean',
    ];

     public function session()
    {
        return $this->belongsTo(Session::class, 'attendance_sessions_id');
    }
}
