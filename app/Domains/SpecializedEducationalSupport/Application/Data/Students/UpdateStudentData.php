<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Students;

use App\Domains\SpecializedEducationalSupport\Application\Data\People\Concerns\NormalizesPersonInput;
use App\Domains\SpecializedEducationalSupport\Application\Rules\ValidCpf;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentStatus;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Student;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class UpdateStudentData extends Data
{
    use NormalizesPersonInput;

    public function __construct(
        public string $name,
        public string $birthDate,
        public Gender $gender,
        public string $email,
        public string $registration,
        public StudentStatus $status,
        public ?string $document = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $entryDate = null,
        public bool $isRepeater = false,
        public ?UploadedFile $photo = null,
        public bool $removePhoto = false,
    ) {}

    public static function rules(): array
    {
        $student = request()->route('student');
        $studentId = $student instanceof Student ? $student->getKey() : null;
        $personId = $student instanceof Student ? $student->person_id : null;

        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'document' => [
                'nullable',
                'string',
                new ValidCpf,
                Rule::unique('people', 'document')->ignore($personId),
            ],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'email' => ['required', 'string', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'digits_between:10,11'],
            'address' => ['nullable', 'string'],
            'registration' => [
                'required',
                'string',
                'max:50',
                Rule::unique('students', 'registration')->ignore($studentId),
            ],
            'entry_date' => ['nullable', 'date', 'after_or_equal:birth_date'],
            'is_repeater' => ['sometimes', 'boolean'],
            'status' => ['required', Rule::enum(StudentStatus::class)],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'remove_photo' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome do aluno é obrigatório.',
            'name.string' => 'O nome do aluno deve ser um texto válido.',
            'name.min' => 'O nome do aluno deve ter ao menos 3 caracteres.',
            'name.max' => 'O nome do aluno não pode ultrapassar 255 caracteres.',
            'document.string' => 'O CPF do aluno deve ser um texto válido.',
            'document.unique' => 'Este CPF já está cadastrado para outra pessoa.',
            'birth_date.required' => 'A data de nascimento do aluno é obrigatória.',
            'birth_date.date' => 'A data de nascimento do aluno deve ser válida.',
            'birth_date.before_or_equal' => 'A data de nascimento não pode estar no futuro.',
            'gender.required' => 'O gênero do aluno é obrigatório.',
            'gender.enum' => 'O gênero selecionado é inválido.',
            'email.required' => 'O e-mail do aluno é obrigatório.',
            'email.string' => 'O e-mail do aluno deve ser um texto válido.',
            'email.email' => 'O e-mail do aluno deve ser válido.',
            'email.max' => 'O e-mail do aluno não pode ultrapassar 255 caracteres.',
            'phone.string' => 'O telefone do aluno deve ser um texto válido.',
            'phone.digits_between' => 'O telefone deve conter 10 ou 11 dígitos.',
            'address.string' => 'O endereço do aluno deve ser um texto válido.',
            'registration.required' => 'A matrícula do aluno é obrigatória.',
            'registration.string' => 'A matrícula do aluno deve ser um texto válido.',
            'registration.max' => 'A matrícula do aluno não pode ultrapassar 50 caracteres.',
            'registration.unique' => 'Esta matrícula já está cadastrada para outro aluno.',
            'entry_date.date' => 'A data de ingresso do aluno deve ser válida.',
            'entry_date.after_or_equal' => 'A data de ingresso não pode ser anterior à data de nascimento.',
            'is_repeater.boolean' => 'A indicação de repetência é inválida.',
            'status.required' => 'Selecione o status do aluno.',
            'status.enum' => 'O status selecionado é inválido.',
            'photo.image' => 'O arquivo da foto deve ser uma imagem válida.',
            'photo.mimes' => 'A foto deve estar nos formatos JPEG, JPG ou PNG.',
            'photo.max' => 'A foto não pode ultrapassar 2 MB.',
            'remove_photo.boolean' => 'A indicação de remoção da foto é inválida.',
        ];
    }
}
