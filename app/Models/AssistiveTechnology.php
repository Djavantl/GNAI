<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AssistiveTechnology extends Model
{
    use HasFactory;

    protected $table = 'assistive_technologies';

    protected $fillable = [
        'name',
        'description',
        'type',
        'quantity',
        'asset_code',
        'conservation_state',
        'target_audience',
        'requires_training',
        'notes',
        'assistive_technology_status_id',
    ];

    protected $casts = [
        'requires_training' => 'boolean',
        'quantity' => 'integer',
    ];

    public function status():BelongsTo
    {
        return $this->belongsTo(
            AssistiveTechnologyStatus::class,
            'assistive_technology_status_id'
        );
    }

}
