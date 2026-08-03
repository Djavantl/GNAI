<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\Professionals;

use App\Domains\SpecializedEducationalSupport\Application\Data\People\Concerns\NormalizesPersonInput;
use App\Domains\SpecializedEducationalSupport\Application\Rules\ValidCpf;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CreateProfessionalData extends Data
{
    use NormalizesPersonInput;

    public function __construct(
        public string $name,
        public string $birthDate,
        public Gender $gender,
        public string $email,
        public string $registration,
        public int $positionId,
        public string $entryDate,
        public ?string $document = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?UploadedFile $photo = null,
        public bool $isAdmin = false,
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
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'email' => [
                'bail',
                'required',
                'string',
                'email:rfc',
                'max:255',
                Rule::unique('people', 'email'),
                Rule::unique('users', 'email'),
            ],
            'phone' => ['nullable', 'string', 'digits_between:10,11'],
            'address' => ['nullable', 'string', 'max:500'],
            'registration' => [
                'required',
                'string',
                'max:50',
                Rule::unique('professionals', 'registration'),
            ],
            'position_id' => ['required', 'integer', Rule::exists('positions', 'id')],
            'entry_date' => ['required', 'date', 'after_or_equal:birth_date'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'is_admin' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome do profissional é obrigatório.',
            'name.string' => 'O nome do profissional deve ser um texto válido.',
            'name.min' => 'O nome do profissional deve ter ao menos 3 caracteres.',
            'name.max' => 'O nome do profissional não pode ultrapassar 255 caracteres.',
            'document.string' => 'O CPF do profissional deve ser um texto válido.',
            'document.unique' => 'Este CPF já está cadastrado para outra pessoa.',
            'birth_date.required' => 'A data de nascimento do profissional é obrigatória.',
            'birth_date.date' => 'A data de nascimento do profissional deve ser válida.',
            'birth_date.before_or_equal' => 'A data de nascimento não pode estar no futuro.',
            'gender.required' => 'O gênero do profissional é obrigatório.',
            'gender.enum' => 'O gênero selecionado é inválido.',
            'email.required' => 'O e-mail do profissional é obrigatório.',
            'email.string' => 'O e-mail do profissional deve ser um texto válido.',
            'email.email' => 'O e-mail do profissional deve ser válido.',
            'email.max' => 'O e-mail do profissional não pode ultrapassar 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado para outro usuário.',
            'phone.string' => 'O telefone do profissional deve ser um texto válido.',
            'phone.digits_between' => 'O telefone deve conter 10 ou 11 dígitos.',
            'address.string' => 'O endereço do profissional deve ser um texto válido.',
            'address.max' => 'O endereço do profissional não pode ultrapassar 500 caracteres.',
            'registration.required' => 'A matrícula do profissional é obrigatória.',
            'registration.string' => 'A matrícula do profissional deve ser um texto válido.',
            'registration.max' => 'A matrícula do profissional não pode ultrapassar 50 caracteres.',
            'registration.unique' => 'Esta matrícula já está cadastrada para outro profissional.',
            'position_id.required' => 'O cargo do profissional é obrigatório.',
            'position_id.integer' => 'O cargo selecionado é inválido.',
            'position_id.exists' => 'O cargo selecionado é inválido.',
            'entry_date.required' => 'A data de ingresso do profissional é obrigatória.',
            'entry_date.date' => 'A data de ingresso do profissional deve ser válida.',
            'entry_date.after_or_equal' => 'A data de ingresso não pode ser anterior à data de nascimento.',
            'photo.image' => 'O arquivo da foto deve ser uma imagem válida.',
            'photo.mimes' => 'A foto deve estar nos formatos JPEG, JPG ou PNG.',
            'photo.max' => 'A foto não pode ultrapassar 2 MB.',
            'is_admin.boolean' => 'A indicação de administrador é inválida.',
        ];
    }
}
