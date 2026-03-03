<?php

namespace App\Models\SpecializedEducationalSupport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Traits\Auditable; 
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    protected function document(): Attribute
    {
        return Attribute::make(
            // Quando sai do banco para a tela: Garante que saia formatado
            get: fn ($value) => $this->formatCpf($value),

            // Quando entra da tela para o banco: Limpa tudo que não é número
            set: fn ($value) => preg_replace('/[^0-9]/', '', $value),
        );
    }

    protected function phone(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            // Quando sai do banco: Formata para (00) 00000-0000
            get: fn ($value) => $this->formatPhone($value),

            // Quando entra no banco: Limpa tudo (salva só números)
            set: fn ($value) => preg_replace('/[^0-9]/', '', $value),
        );
    }

    private function formatPhone($value)
    {
        if (!$value) return null;
        $value = preg_replace('/[^0-9]/', '', $value);
        
        if (strlen($value) === 11) {
            return preg_replace("/(\d{2})(\d{5})(\d{4})/", "($1) $2-$3", $value);
        }
        
        if (strlen($value) === 10) {
            return preg_replace("/(\d{2})(\d{4})(\d{4})/", "($1) $2-$3", $value);
        }

        return $value;
    }

    private function formatCpf($value)
    {
        if (!$value) return null;
        $value = preg_replace('/[^0-9]/', '', $value);
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "$1.$2.$3-$4", $value);
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
