<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\SpecializedEducationalSupport\Domain\DTOs\People\CreatePersonDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\People\UpdatePersonDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidPerson;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Cpf;
use App\Domains\SpecializedEducationalSupport\Domain\ValueObjects\Phone;
use App\Models\AuditLog;
use App\Models\SpecializedEducationalSupport\Guardian;
use App\Models\SpecializedEducationalSupport\Professional;
use App\Models\SpecializedEducationalSupport\Teacher;
use App\Models\Traits\Auditable;
use Carbon\CarbonImmutable;
use Database\Factories\Domains\SpecializedEducationalSupport\PersonFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Throwable;

#[UseFactory(PersonFactory::class)]
final class Person extends Model
{
    use Auditable;
    use HasFactory;

    protected $table = 'people';

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
        'gender' => Gender::class,
    ];

    /**
     * @throws InvalidPerson
     */
    public static function register(CreatePersonDTO $data): self
    {
        return new self(self::attributesFrom($data));
    }

    /**
     * @throws InvalidPerson
     */
    public function revise(UpdatePersonDTO $data): void
    {
        $this->fill(self::attributesFrom($data));
    }

    public static function getAuditLabels(): array
    {
        return [
            'name' => 'Nome Completo',
            'document' => 'CPF/Documento',
            'birth_date' => 'Data de Nascimento',
            'gender' => 'Gênero',
            'email' => 'E-mail',
            'phone' => 'Telefone',
            'address' => 'Endereço',
            'photo' => 'Foto de Perfil',
        ];
    }

    public static function formatAuditValue(string $field, mixed $value): ?string
    {
        if ($field === 'gender') {
            $gender = $value instanceof Gender
                ? $value
                : Gender::tryFrom((string) $value);

            return $gender?->label() ?? (string) $value;
        }

        if ($field === 'birth_date' && filled($value)) {
            return CarbonImmutable::parse($value)->format('d/m/Y');
        }

        return null;
    }

    public function getGenderLabelAttribute(): string
    {
        return $this->gender->label();
    }

    public function getPhotoUrlAttribute(): string
    {
        return $this->photo
            ? asset('storage/'.$this->photo)
            : asset('images/default-user.jpg');
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'person_id');
    }

    public function professional(): HasOne
    {
        return $this->hasOne(Professional::class, 'person_id');
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'person_id');
    }

    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class, 'person_id');
    }

    public function logs(): MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    protected function document(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?string => self::formatDocument($value),
            set: fn (?string $value): ?string => Cpf::fromNullable($value)?->value(),
        );
    }

    protected function phone(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value): ?string => self::formatPhone($value),
            set: fn (?string $value): ?string => Phone::fromNullable($value)?->value(),
        );
    }

    /**
     * @return array<string, string|null>
     *
     * @throws InvalidPerson
     */
    private static function attributesFrom(CreatePersonDTO|UpdatePersonDTO $data): array
    {
        return [
            'name' => self::normalizeName($data->name),
            'document' => $data->document?->value(),
            'birth_date' => self::normalizeBirthDate($data->birthDate),
            'gender' => $data->gender->value,
            'email' => self::normalizeEmail($data->email),
            'phone' => $data->phone?->value(),
            'address' => self::normalizeNullableText($data->address),
            'photo' => self::normalizePhoto($data->photo),
        ];
    }

    /**
     * @throws InvalidPerson
     */
    private static function normalizeName(string $name): string
    {
        $name = trim($name);
        $length = mb_strlen($name);

        if ($length < 3) {
            throw new InvalidPerson('O nome deve ter ao menos 3 caracteres.');
        }

        if ($length > 255) {
            throw new InvalidPerson('O nome não pode ultrapassar 255 caracteres.');
        }

        return $name;
    }

    /**
     * @throws InvalidPerson
     */
    private static function normalizeBirthDate(string $birthDate): string
    {
        try {
            $date = CarbonImmutable::parse($birthDate)->startOfDay();
        } catch (Throwable) {
            throw new InvalidPerson('A data de nascimento deve ser válida.');
        }

        if ($date->isFuture()) {
            throw new InvalidPerson('A data de nascimento não pode estar no futuro.');
        }

        return $date->toDateString();
    }

    /**
     * @throws InvalidPerson
     */
    private static function normalizeEmail(?string $email): ?string
    {
        $email = self::normalizeNullableText($email);

        if ($email === null) {
            return null;
        }

        $email = mb_strtolower($email);

        if (mb_strlen($email) > 255 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidPerson('O e-mail deve ser válido.');
        }

        return $email;
    }

    private static function normalizeNullableText(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private static function formatDocument(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value) ?? '';

        return preg_replace(
            '/(\d{3})(\d{3})(\d{3})(\d{2})/',
            '$1.$2.$3-$4',
            $digits,
        ) ?? $digits;
    }

    private static function formatPhone(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $digits = preg_replace('/\D/', '', $value) ?? '';

        if (strlen($digits) === 11) {
            return preg_replace(
                '/(\d{2})(\d{5})(\d{4})/',
                '($1) $2-$3',
                $digits,
            ) ?? $digits;
        }

        if (strlen($digits) === 10) {
            return preg_replace(
                '/(\d{2})(\d{4})(\d{4})/',
                '($1) $2-$3',
                $digits,
            ) ?? $digits;
        }

        return $digits;
    }

    /**
     * @throws InvalidPerson
     */
    private static function normalizePhoto(?string $photo): ?string
    {
        $photo = self::normalizeNullableText($photo);

        if ($photo !== null && mb_strlen($photo) > 255) {
            throw new InvalidPerson('O caminho da foto não pode ultrapassar 255 caracteres.');
        }

        return $photo;
    }
}
