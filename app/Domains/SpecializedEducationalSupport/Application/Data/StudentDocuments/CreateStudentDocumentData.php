<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\StudentDocuments;

use App\Domains\SpecializedEducationalSupport\Domain\Enums\StudentDocumentType;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CreateStudentDocumentData extends Data
{
    public function __construct(
        public string $title,
        public StudentDocumentType $type,
        public UploadedFile $file,
    ) {}

    public static function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(StudentDocumentType::class)],
            'file' => [
                'required',
                'file',
                'mimes:pdf,doc,docx,jpg,jpeg,png',
                'max:10240',
            ],
        ];
    }

    public static function messages(): array
    {
        return [
            'title.required' => 'O título do documento é obrigatório.',
            'title.string' => 'O título do documento deve ser um texto válido.',
            'title.max' => 'O título do documento não pode ultrapassar 255 caracteres.',
            'type.required' => 'O tipo do documento é obrigatório.',
            'type.enum' => 'O tipo de documento selecionado é inválido.',
            'file.required' => 'Selecione o arquivo do documento.',
            'file.file' => 'O arquivo enviado é inválido.',
            'file.mimes' => 'O arquivo deve estar no formato PDF, DOC, DOCX, JPG, JPEG ou PNG.',
            'file.max' => 'O arquivo não pode ultrapassar 10 MB.',
        ];
    }
}
