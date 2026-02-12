<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SpecializedEducationalSupport\Position;

class Permission extends Model
{
    protected $fillable = ['name', 'slug'];

    public function positions()
    {
        return $this->belongsToMany(Position::class, 'permission_position');
    }
}