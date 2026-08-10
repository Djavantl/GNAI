<?php

declare(strict_types=1);

namespace App\Domains\Auth\Application\Data\Profiles;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Application\Data\People\Concerns\NormalizesPersonInput;
use App\Domains\SpecializedEducationalSupport\Application\Rules\ValidCpf;
use App\Domains\SpecializedEducationalSupport\Domain\Enums\Gender;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class UpdateProfileData extends Data
{
    use NormalizesPersonInput;

    public function __construct(
        public string $name,
        public string $registration,
        public string $birthDate,
        public Gender $gender,
        public string $email,
        public ?string $document = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?UploadedFile $photo = null,
        public bool $removePhoto = false,
        public ?string $password = null,
    ) {}

    public static function rules(): array
    {
        $user = request()->user();
        $personId = $user instanceof User
            ? ($user->professional?->person_id ?? $user->teacher?->person_id)
            : null;
        $registrationTable = $user instanceof User && $user->professional ? 'professionals' : 'teachers';
        $registrationId = $user instanceof User
            ? ($user->professional?->id ?? $user->teacher?->id)
            : null;

        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'registration' => [
                'required',
                'string',
                'max:50',
                Rule::unique($registrationTable, 'registration')->ignore($registrationId),
            ],
            'document' => [
                'nullable',
                'string',
                new ValidCpf,
                Rule::unique('people', 'document')->ignore($personId),
            ],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', Rule::enum(Gender::class)],
            'phone' => ['nullable', 'string', 'digits_between:10,11'],
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                Rule::unique('people', 'email')->ignore($personId),
                Rule::unique('users', 'email')->ignore($user instanceof User ? $user->getKey() : null),
            ],
            'address' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'remove_photo' => ['sometimes', 'boolean'],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.string' => 'O nome deve ser um texto válido.',
            'name.min' => 'O nome deve ter ao menos 3 caracteres.',
            'name.max' => 'O nome não pode ultrapassar 255 caracteres.',
            'document.unique' => 'Este CPF/Documento já está em uso.',
            'registration.required' => 'A matrícula é obrigatória.',
            'registration.string' => 'A matrícula deve ser um texto válido.',
            'registration.max' => 'A matrícula não pode ultrapassar 50 caracteres.',
            'registration.unique' => 'Esta matrícula já está em uso.',
            'birth_date.required' => 'A data de nascimento é obrigatória.',
            'birth_date.date' => 'A data de nascimento deve ser válida.',
            'birth_date.before' => 'A data de nascimento deve ser anterior a hoje.',
            'gender.required' => 'O gênero é obrigatório.',
            'gender.enum' => 'O gênero selecionado é inválido.',
            'phone.string' => 'O telefone deve ser um texto válido.',
            'phone.digits_between' => 'O telefone deve conter 10 ou 11 dígitos.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ser válido.',
            'email.max' => 'O e-mail não pode ultrapassar 255 caracteres.',
            'email.unique' => 'Este e-mail já está cadastrado em outra conta.',
            'address.string' => 'O endereço deve ser um texto válido.',
            'address.max' => 'O endereço não pode ultrapassar 500 caracteres.',
            'photo.image' => 'A foto deve ser uma imagem.',
            'photo.mimes' => 'A foto deve estar nos formatos jpg, jpeg ou png.',
            'photo.max' => 'A foto não pode ser maior que 2MB.',
            'remove_photo.boolean' => 'O campo de remoção da foto é inválido.',
            'password.confirmed' => 'As senhas digitadas não conferem.',
            'password.min' => 'A nova senha deve ter pelo menos 8 caracteres.',
        ];
    }
}
