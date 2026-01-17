<?php

namespace App\Models\InclusiveRadar;

use App\Models\InclusiveRadar\TypeAttribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceAttributeValue extends Model
{
    use HasFactory;

    protected $table = 'resource_attribute_values';

    protected $fillable = [
        'resource_id',
        'resource_type',
        'attribute_id',
        'value',
    ];

    public function attribute()
    {
        return $this->belongsTo(TypeAttribute::class, 'attribute_id');
    }

    public function resource()
    {
        return $this->morphTo(null, 'resource_type', 'resource_id');
    }
}
