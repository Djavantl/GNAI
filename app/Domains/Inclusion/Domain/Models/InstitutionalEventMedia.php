<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Foto da galeria de uma publicação, sempre acompanhada de texto alternativo.
 */
final class InstitutionalEventMedia extends Model
{
    protected $table = 'institutional_event_media';

    protected $fillable = [
        'institutional_event_id',
        'path',
        'alt_text',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(InstitutionalEvent::class, 'institutional_event_id');
    }

    public function url(): string
    {
        return asset('storage/'.$this->path);
    }
}
