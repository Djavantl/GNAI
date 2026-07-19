<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use App\Domains\InclusiveRadar\Domain\Enums\WaitlistStatus;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class Waitlist extends Model
{
    protected $table = 'waitlists';

    protected $fillable = [
        'waitlistable_id',
        'waitlistable_type',
        'student_id',
        'professional_id',
        'user_id',
        'requested_at',
        'status',
        'observation',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'status' => WaitlistStatus::class,
    ];

    public function waitlistable(): MorphTo
    {
        return $this->morphTo()->withTrashed();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
