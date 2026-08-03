<?php

declare(strict_types=1);

namespace App\Domains\SpecializedEducationalSupport\Application\Data\PeiDisciplines;

use App\Domains\Auth\Domain\Models\User;
use App\Domains\SpecializedEducationalSupport\Domain\Models\Pei;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class CreatePeiDisciplineData extends Data
{
    public function __construct(
        public int $teacherId,
        public int $disciplineId,
        public string $specificObjectives,
        public string $contentProgrammatic,
        public string $methodologies,
        public string $evaluations,
        public ?string $opinion = null,
        public ?string $complementaryRecords = null,
    ) {}

    public static function rules(): array
    {
        $user = request()->user();
        $teacherId = $user instanceof User && $user->teacher_id ? (int) $user->teacher_id : null;
        $pei = request()->route('pei');
        $peiId = $pei instanceof Pei ? (int) $pei->getKey() : $pei;

        return [
            'teacher_id' => [
                'required',
                'integer',
                Rule::exists('teachers', 'id'),
                Rule::when($teacherId !== null, Rule::in([$teacherId])),
            ],
            'discipline_id' => [
                'required',
                'integer',
                Rule::exists('disciplines', 'id'),
                Rule::unique('pei_disciplines', 'discipline_id')
                    ->where('pei_id', $peiId),
            ],
            'specific_objectives' => ['required', 'string'],
            'content_programmatic' => ['required', 'string'],
            'methodologies' => ['required', 'string'],
            'evaluations' => ['required', 'string'],
            'opinion' => ['nullable', 'string'],
            'complementary_records' => ['nullable', 'string'],
        ];
    }

    public static function messages(): array
    {
        return [
            'teacher_id.required' => 'O professor responsável é obrigatório.',
            'teacher_id.integer' => 'O professor responsável informado é inválido.',
            'teacher_id.exists' => 'O professor responsável informado é inválido.',
            'teacher_id.in' => 'O professor responsável informado é inválido.',
            'discipline_id.required' => 'A disciplina é obrigatória.',
            'discipline_id.integer' => 'A disciplina informada é inválida.',
            'discipline_id.exists' => 'A disciplina informada é inválida.',
            'discipline_id.unique' => 'Já existe uma adaptação para essa disciplina nesse PEI.',
            'specific_objectives.required' => 'Os objetivos específicos são obrigatórios.',
            'specific_objectives.string' => 'Os objetivos específicos devem ser um texto válido.',
            'content_programmatic.required' => 'O conteúdo programático é obrigatório.',
            'content_programmatic.string' => 'O conteúdo programático deve ser um texto válido.',
            'methodologies.required' => 'As metodologias e estratégias são obrigatórias.',
            'methodologies.string' => 'As metodologias e estratégias devem ser um texto válido.',
            'evaluations.required' => 'O processo de avaliação é obrigatório.',
            'evaluations.string' => 'O processo de avaliação deve ser um texto válido.',
            'opinion.string' => 'O parecer deve ser um texto válido.',
            'complementary_records.string' => 'Os registros complementares devem ser um texto válido.',
        ];
    }
}
