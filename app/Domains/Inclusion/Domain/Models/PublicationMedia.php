<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Foto da galeria de uma publicação, sempre acompanhada de texto alternativo.
 */
final class PublicationMedia extends Model
{
    protected $table = 'publication_media';

    protected $fillable = [
        'publication_id',
        'path',
        'alt_text',
        'position',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class, 'publication_id');
    }

    public function url(): string
    {
        return asset('storage/'.$this->path);
    }
}
