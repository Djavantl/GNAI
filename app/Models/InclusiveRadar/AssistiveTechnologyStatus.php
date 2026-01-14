<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssistiveTechnologyStatus extends Model
{
    use HasFactory;

    protected $table = 'assistive_technology_statuses';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assistiveTechnologies()
    {
        return $this->hasMany(
            AssistiveTechnology::class,
            'assistive_technology_status_id'
        );
    }
}
