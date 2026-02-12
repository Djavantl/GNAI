<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Enums\Priority;

class Pendency extends Model
{
    use HasFactory;

    protected $table = 'pendencies';

    protected $fillable = [
        'created_by',
        'assigned_to',
        'title',
        'description',
        'priority',
        'due_date',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'due_date'     => 'date',
        'priority'     => Priority::class,
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedProfessional()
    {
        return $this->belongsTo(Professional::class, 'assigned_to');
    }

    public function scopePending($query)
    {
        return $query->where('is_completed', false);
    }

    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at
            ? $this->created_at->format('d/m/Y H:i')
            : '—';
    }

    public function getUpdatedAtFormattedAttribute()
    {
        return $this->updated_at
            ? $this->updated_at->format('d/m/Y H:i')
            : '—';
    }

    public function getDueDateFormattedAttribute()
    {
        return $this->due_date
            ? $this->due_date->format('d/m/Y')
            : '—';
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'is_completed' => true,
        ]);
    }

}
