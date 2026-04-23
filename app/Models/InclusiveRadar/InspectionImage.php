<?php

namespace App\Models\InclusiveRadar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

/**
 * RF: evidências visuais anexadas às vistorias.
 * Uso: timelines, detalhes da inspeção e limpeza automática do storage.
 */
class InspectionImage extends Model
{
    use HasFactory;

    /**
     * Persistência:
     * Mantém os metadados do arquivo persistidos junto à vistoria.
     */

    protected $fillable = [
        'inspection_id',
        'path',
        'original_name',
        'mime_type',
        'size'
    ];

    /**
     * Eventos de Modelo:
     * Garante remoção física do arquivo ao excluir o registro da imagem.
     */

    protected static function booted()
    {
        static::deleted(function ($image) {
            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }
        });
    }

    /**
     * Relacionamentos:
     * A imagem sempre pertence a uma única inspeção.
     */

    public function inspection(): BelongsTo
    {
        return $this->belongsTo(Inspection::class, 'inspection_id');
    }
}
