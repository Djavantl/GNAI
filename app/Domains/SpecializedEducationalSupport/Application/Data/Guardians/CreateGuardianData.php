<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Guardians;

use App\Domains\SpecializedEducationalSupport\Application\Data\People\Concerns\NormalizesPersonInput;
use App\Domains\SpecializedEducationalSupport\Application\Rules\ValidCpf;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\GuardianRelationship;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CreateGuardianData extends Data
{
    use NormalizesPersonInput;

    public function __construct(
        public string $name,
        public string $birthDate,
        public Gender $gender,
        public string $phone,
        public GuardianRelationship $relationship,
        public ?string $document = null,
        public ?string $email = null,
        public ?string $address = null,
        public ?UploadedFile $photo = null,
    ) {}

    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'document' => [
                'bail',
                'nullable',
                'string',
                new ValidCpf,
                Rule::unique('people', 'document'),
            ],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'email' => ['nullable', 'string', 'email:rfc', 'max:255'],
            'phone' => ['required', 'string', 'digits_between:10,11'],
            'address' => ['nullable', 'string', 'max:500'],
            'relationship' => ['required', Rule::enum(GuardianRelationship::class)],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome do responsável é obrigatório.',
            'name.string' => 'O nome do responsável deve ser um texto válido.',
            'name.min' => 'O nome do responsável deve ter ao menos 3 caracteres.',
            'name.max' => 'O nome do responsável não pode ultrapassar 255 caracteres.',
            'document.string' => 'O CPF do responsável deve ser um texto válido.',
            'document.unique' => 'Este CPF já está cadastrado para outra pessoa.',
            'birth_date.required' => 'A data de nascimento do responsável é obrigatória.',
            'birth_date.date' => 'A data de nascimento do responsável deve ser válida.',
            'birth_date.before' => 'A data de nascimento deve ser anterior à data atual.',
            'gender.required' => 'O gênero do responsável é obrigatório.',
            'gender.enum' => 'O gênero selecionado é inválido.',
            'email.string' => 'O e-mail do responsável deve ser um texto válido.',
            'email.email' => 'O e-mail do responsável deve ser válido.',
            'email.max' => 'O e-mail do responsável não pode ultrapassar 255 caracteres.',
            'phone.required' => 'O telefone do responsável é obrigatório.',
            'phone.string' => 'O telefone do responsável deve ser um texto válido.',
            'phone.digits_between' => 'O telefone deve conter 10 ou 11 dígitos.',
            'address.string' => 'O endereço do responsável deve ser um texto válido.',
            'address.max' => 'O endereço do responsável não pode ultrapassar 500 caracteres.',
            'relationship.required' => 'O parentesco é obrigatório.',
            'relationship.enum' => 'O parentesco selecionado é inválido.',
            'photo.image' => 'O arquivo da foto deve ser uma imagem válida.',
            'photo.mimes' => 'A foto deve estar nos formatos JPEG, JPG, PNG ou WEBP.',
            'photo.max' => 'A foto não pode ultrapassar 2 MB.',
        ];
    }
}
