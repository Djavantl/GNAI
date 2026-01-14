<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    /**
     * O Laravel usa a convenção de plural em inglês.
     * Como seu Model é 'Person', ele já buscará a tabela 'people' automaticamente.
     */

    // 1. Campos que podem ser preenchidos via formulário
    protected $fillable = [
        'name',
        'document',
        'birth_date',
        'gender',
        'email',
        'phone',
        'address',
    ];

    // 2. Conversão de tipos (Casting)
    protected $casts = [
        'birth_date' => 'date', // Transforma a string do banco em um objeto Carbon (Data)
    ];

    /**
     * 3. Helper para as opções de Gênero (opcional, mas recomendado)
     * Isso ajuda você a listar as opções no formulário e exibir o nome correto.
     */
    public static function genderOptions(): array
    {
        return [
            'male' => 'Masculino',
            'female' => 'Feminino',
            'other' => 'Outro',
            'not_specified' => 'Não informado',
        ];
    }

    /**
     * 4. Acessor para exibir o gênero formatado (opcional)
     * Exemplo de uso: $person->gender_label
     */
    public function getGenderLabelAttribute(): string
    {
        return self::genderOptions()[$this->gender] ?? $this->gender;
    }
}
