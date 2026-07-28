<?php

namespace App\Models\SpecializedEducationalSupport;

use App\Domains\SpecializedEducationalSupport\Domain\Models\Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PedagogicalRecord extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'attendance_session_id',
        'duration',
        'is_present',
        'absence_reason',
        'planned_performed_activities',
        'pedagogical_record',
        'resources_used',
        'general_observations',
    ];

    protected $casts = [
        'is_present' => 'boolean',
    ];

    public function attendanceSession(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'attendance_session_id')->withTrashed();
    }
}
