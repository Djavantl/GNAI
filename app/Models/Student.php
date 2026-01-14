<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'person_id',
        'registration',
        'student_code',
        'entry_date',
        'status',
        'education_level',
        'modality',
        'notes',
    ];

    /*
     * Relacionamentos
     */
    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    /*
     * Helpers
     */
    public static function statusOptions(): array
    {
        return [
            'active'    => 'Ativo',
            'locked'    => 'Trancado',
            'completed' => 'Concluído',
            'dropped'   => 'Evadido',
        ];
    }
}
