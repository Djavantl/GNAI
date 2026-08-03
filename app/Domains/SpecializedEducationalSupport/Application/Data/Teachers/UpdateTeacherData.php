<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Teachers;

use App\Domains\SpecializedEducationalSupport\Application\Data\People\Concerns\NormalizesPersonInput;
use App\Domains\SpecializedEducationalSupport\Application\Rules\ValidCpf;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Teacher;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateTeacherData extends Data
{
    use NormalizesPersonInput;

    public function __construct(
        public string $name,
        public string $birthDate,
        public string $gender,
        public string $email,
        public string $registration,
        public ?string $document = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?UploadedFile $photo = null,
        public bool $removePhoto = false,
    ) {}

    public static function rules(): array
    {
        $teacher = request()->route('teacher');
        $teacherId = $teacher instanceof Teacher ? (int) $teacher->getKey() : null;
        $personId = $teacher instanceof Teacher ? (int) $teacher->person_id : null;
        $userId = $teacher instanceof Teacher ? $teacher->user()->value('id') : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'document' => [
                'bail',
                'nullable',
                'string',
                new ValidCpf,
                Rule::unique('people', 'document')->ignore($personId),
            ],
            'birth_date' => ['required', 'date'],
            'gender' => ['required', 'in:male,female,other,not_specified'],
            'email' => [
                'required',
                'email',
                Rule::unique('people', 'email')->ignore($personId),
                Rule::unique('users', 'email')->ignore($userId === null ? null : (int) $userId),
            ],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'registration' => [
                'required',
                'string',
                'max:50',
                Rule::unique('teachers', 'registration')->ignore($teacherId),
            ],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome do professor é obrigatório.',
            'name.string' => 'O nome do professor deve ser um texto válido.',
            'name.max' => 'O nome do professor não pode ultrapassar 255 caracteres.',
            'document.string' => 'O CPF do professor deve ser um texto válido.',
            'document.unique' => 'Este CPF já está cadastrado para outra pessoa.',
            'birth_date.required' => 'A data de nascimento do professor é obrigatória.',
            'birth_date.date' => 'A data de nascimento do professor deve ser válida.',
            'gender.required' => 'O gênero do professor é obrigatório.',
            'gender.in' => 'O gênero selecionado é inválido.',
            'email.required' => 'O e-mail do professor é obrigatório.',
            'email.email' => 'O e-mail do professor deve ser válido.',
            'email.unique' => 'Este e-mail já está cadastrado para outra pessoa.',
            'phone.string' => 'O telefone do professor deve ser um texto válido.',
            'address.string' => 'O endereço do professor deve ser um texto válido.',
            'registration.required' => 'A matrícula do professor é obrigatória.',
            'registration.string' => 'A matrícula do professor deve ser um texto válido.',
            'registration.max' => 'A matrícula do professor não pode ultrapassar 50 caracteres.',
            'registration.unique' => 'Esta matrícula já está em uso.',
            'photo.image' => 'O arquivo da foto deve ser uma imagem válida.',
            'photo.mimes' => 'A foto deve estar nos formatos jpeg, jpg ou png.',
            'photo.max' => 'A foto não pode ultrapassar 2 MB.',
            'remove_photo.boolean' => 'O campo de remoção da foto é inválido.',
        ];
    }

}
