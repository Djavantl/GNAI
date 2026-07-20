<?php

declare(strict_types=1);

namespace App\Domains\InclusiveRadar\Domain\Models;

use App\Domains\InclusiveRadar\Domain\DTOs\Inspections\CreateInspectionDTO;
use App\Domains\InclusiveRadar\Domain\Enums\ConservationState;
use App\Domains\InclusiveRadar\Domain\Enums\InspectionType;
use Database\Factories\Domains\InclusiveRadar\InspectionFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[UseFactory(InspectionFactory::class)]
final class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspectable_id',
        'inspectable_type',
        'state',
        'status',
        'inspection_date',
        'description',
        'type',
        'user_id',
    ];

    protected $casts = [
        'inspection_date' => 'date',
        'state' => ConservationState::class,
        'type' => InspectionType::class,
    ];

    public static function register(CreateInspectionDTO $data): self
    {
        return new self([
            'state' => $data->state,
            'status' => $data->status,
            'inspection_date' => $data->date,
            'description' => $data->description,
            'type' => $data->type,
            'user_id' => $data->registeredBy,
        ]);
    }

    public function inspectable(): MorphTo
    {
        return $this->morphTo();
    }

    public function images(): HasMany
    {
        return $this->hasMany(InspectionImage::class, 'inspection_id');
    }
}
