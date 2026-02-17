<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $table = 'audit_logs';

    // Campos que podem ser preenchidos via create()
    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    // Casts para transformar JSON em array automaticamente
    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    // RELACIONAMENTOS

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }
}
