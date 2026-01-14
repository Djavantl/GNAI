<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarrierStatus extends Model
{
    use HasFactory;

    protected $table = 'barrier_statuses';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function barriers()
    {
        return $this -> hasMany(Barrier::class,
        'barrier_status_id');
    }

}
