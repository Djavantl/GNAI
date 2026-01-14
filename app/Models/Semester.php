<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Semester extends Model
{
    use HasFactory;

    protected $table = 'semesters';

    protected $fillable = [
        'year',
        'term',
        'label',
        'start_date',
        'end_date',
        'is_current',
    ];

    protected $casts = [
        'year'       => 'integer',
        'term'       => 'integer',
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Retorna o semestre atual do sistema
     */
    public static function current(): ?self
    {
        return self::where('is_current', true)->first();
    }

    /**
     * Scope para buscar por ano
     */
    public function scopeByYear($query, int $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope para buscar por semestre (term)
     */
    public function scopeByTerm($query, int $term)
    {
        return $query->where('term', $term);
    }
}
