<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeAttributeAssignment extends Model
{
    use HasFactory;

    protected $table = 'type_attribute_assignments';

    protected $fillable = [
        'type_id',
        'attribute_id',
    ];

    public function type()
    {
        return $this->belongsTo(ResourceType::class, 'type_id');
    }

    public function attribute()
    {
        return $this->belongsTo(TypeAttribute::class, 'attribute_id');
    }
}
