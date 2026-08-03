<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Domain\Models;

use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Guardians\CreateGuardianDTO;
use App\Domains\SpecializedEducationalSupport\Domain\DTOs\Guardians\UpdateGuardianDTO;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\GuardianRelationship;
use App\Domains\SpecializedEducationalSupport\Domain\Exceptions\InvalidGuardian;
use Database\Factories\Domains\SpecializedEducationalSupport\GuardianFactory;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UseFactory(GuardianFactory::class)]
final class Guardian extends Model
{
    use HasFactory;

    protected $table = 'student_guardians';

    protected $fillable = [
        'student_id',
        'person_id',
        'relationship',
    ];

    protected $casts = [
        'relationship' => GuardianRelationship::class,
    ];

    /**
     * @throws InvalidGuardian
     */
    public static function register(Student $student, Person $person, CreateGuardianDTO $data): self
    {
        if (! $student->exists || ! $person->exists) {
            throw new InvalidGuardian(
                'O responsável deve ser vinculado a um aluno e a uma pessoa persistidos.'
            );
        }

        return new self([
            'student_id' => $student->getKey(),
            'person_id' => $person->getKey(),
            'relationship' => $data->relationship->value,
        ]);
    }

    public function revise(UpdateGuardianDTO $data): void
    {
        $this->relationship = $data->relationship->value;
    }

    /**
     * @throws InvalidGuardian
     */
    public function ensureBelongsTo(Student $student): void
    {
        if ((int) $this->student_id !== (int) $student->getKey()) {
            throw new InvalidGuardian('Este responsável não está vinculado ao aluno informado.');
        }
    }

    public function relationshipLabel(): string
    {
        return $this->relationship->label();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_id');
    }
}
