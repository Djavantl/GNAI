<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;

class Methodology extends Model
{
    protected $fillable = [
        'pei_id', 
        'description', 
        'resources_used'
    ];

    public function pei() 
    { 
        return $this->belongsTo(Pei::class); 
    }
}