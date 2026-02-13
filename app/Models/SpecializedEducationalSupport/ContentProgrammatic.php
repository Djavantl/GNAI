<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Model;

class ContentProgrammatic extends Model
{
    protected $table = 'content_programmatic';

    protected $fillable = [
        'pei_id', 
        'title', 
        'description'
    ];

    public function pei() 
    { 
        return $this->belongsTo(Pei::class); 
    }
}