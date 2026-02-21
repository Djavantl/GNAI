<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Traits\Auditable; // 1. Importar a Trait
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Models\AuditLog;

class Person extends Model
{
    // 2. Adicionar a Trait Auditable aqui
    use HasFactory, Auditable;

    protected $fillable = [
        'name',
        'document',
        'birth_date',
        'gender',
        'email',
        'phone',
        'address',
        'photo',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /**
     * Relacionamento com Logs de Auditoria
     */
    public function logs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * Labels para o relatório de auditoria
     */
    public static function getAuditLabels(): array
    {
        return [
            'name'       => 'Nome Completo',
            'document'   => 'CPF/Documento',
            'birth_date' => 'Data de Nascimento',
            'gender'     => 'Gênero',
            'email'      => 'E-mail',
            'phone'      => 'Telefone',
            'address'    => 'Endereço',
            'photo'      => 'Foto de Perfil',
        ];
    }

    /**
     * Formatação de valores para o Log
     */
    public static function formatAuditValue(string $field, $value): ?string
    {
        if ($field === 'gender') {
            return self::genderOptions()[$value] ?? $value;
        }

        if ($field === 'birth_date' && $value) {
            return \Carbon\Carbon::parse($value)->format('d/m/Y');
        }

        return null;
    }
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

    public function getPhotoUrlAttribute()
    {
        // Se existe algo no banco
        if ($this->photo) {
            // O Storage::url() adiciona automaticamente o prefixo '/storage/'
            return asset('storage/' . $this->photo);
        }

        // Se não existir, o caminho da imagem padrão
        return asset('images/default-user.jpg');
    }

    /**
     * 4. Acessor para exibir o gênero formatado (opcional)
     * Exemplo de uso: $person->gender_label
     */
    public function getGenderLabelAttribute(): string
    {
        return self::genderOptions()[$this->gender] ?? $this->gender;
    }

    public function guardians()
    {
        return $this->hasMany(Guardian::class);
    }

}
