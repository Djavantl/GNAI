<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Participante de uma publicação: um profissional cadastrado ou uma pessoa
 * avulsa identificada apenas pelo nome, com a forma de atuação.
 */
final class PublicationSpeaker extends Model
{
    public $timestamps = false;

    protected $table = 'publication_speakers';

    protected $fillable = [
        'publication_id',
        'professional_id',
        'name',
        'position',
        'participation',
    ];

    protected $casts = [
        'position' => 'integer',
    ];

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class, 'publication_id');
    }

    public function professional(): BelongsTo
    {
        return $this->belongsTo(Professional::class, 'professional_id');
    }

    public function displayName(): string
    {
        return $this->professional?->person?->name ?? $this->name ?? 'Participante';
    }

    public function isGuest(): bool
    {
        return $this->professional_id === null;
    }
}
