<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];

    // protected static function booted() {
    //     static::addGlobalScope('is_active', function ($query) {
    //         $query->where('is_active', true);
    //     });
    // }
}
