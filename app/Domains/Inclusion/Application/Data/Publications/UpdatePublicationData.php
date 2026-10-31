<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Data\Publications;

use App\Domains\Inclusion\Domain\Enums\PublicationType;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Attributes\Validation\Unique;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\References\RouteParameterReference;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdatePublicationData extends Data
{
    /**
     * @param  list<array{id?: int|string|null, name?: string|null, participation?: string|null}>  $speakers
     * @param  list<int|string>  $tagIds
     * @param  list<array{file?: UploadedFile, alt?: string|null}>  $gallery
     * @param  list<array{id?: int|string, alt?: string|null, remove?: bool|string|null}>  $galleryExisting
     */
    public function __construct(
        public string $title,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?string $startTime = null,
        public ?string $endTime = null,
        public ?string $location = null,
        public ?string $description = null,
        public ?string $organizer = null,
        public ?string $audience = null,
        public bool $isActive = false,
        public bool $hasSchedule = true,
        public string $type = 'event',
        public ?string $summary = null,
        #[Unique('publications', 'slug', ignore: new RouteParameterReference('publication', 'id'))]
        public ?string $slug = null,
        public ?int $authorId = null,
        public array $speakers = [],
        public array $tagIds = [],
        public ?string $linkUrl = null,
        public ?string $linkLabel = null,
        public ?UploadedFile $cover = null,
        public ?string $coverAlt = null,
        public bool $removeCover = false,
        public array $gallery = [],
        public array $galleryExisting = [],
        public ?string $publishedAt = null,
        public bool $isFeatured = false,
        public ?string $seoTitle = null,
        public ?string $seoDescription = null,
    ) {}

    /**
     * Datas, horários e local só são exigidos quando a publicação possui período (has_schedule).
     */
    public static function rules(ValidationContext $context): array
    {
        $required = filter_var($context->payload['has_schedule'] ?? true, FILTER_VALIDATE_BOOL) ? 'required' : 'nullable';

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:160',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],
            'summary' => ['nullable', 'string', 'max:500'],
            'type' => ['sometimes', 'required', Rule::enum(PublicationType::class)],
            'has_schedule' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
            'start_date' => [$required, 'date'],
            'end_date' => [$required, 'date', 'after_or_equal:start_date'],
            'start_time' => [$required, 'date_format:H:i'],
            'end_time' => [$required, 'date_format:H:i', 'after_or_equal:start_time'],
            'location' => [$required, 'string', 'max:255'],
            'organizer' => ['nullable', 'string', 'max:255'],
            'audience' => ['nullable', 'string', 'max:255'],
            'author_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'speakers' => ['sometimes', 'array'],
            'speakers.*.id' => ['nullable', 'required_without:speakers.*.name', 'integer', 'distinct', Rule::exists('professionals', 'id')],
            'speakers.*.name' => ['nullable', 'string', 'max:150'],
            'speakers.*.participation' => ['nullable', 'string', 'max:100'],
            'tag_ids' => ['sometimes', 'array'],
            'tag_ids.*' => ['integer', 'distinct', Rule::exists('tags', 'id')],
            'link_url' => ['nullable', 'url:http,https', 'max:500'],
            'link_label' => ['nullable', 'string', 'max:80'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'cover_alt' => ['nullable', 'required_with:cover', 'string', 'max:255'],
            'remove_cover' => ['sometimes', 'boolean'],
            'gallery' => ['sometimes', 'array', 'max:30'],
            'gallery.*.file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'gallery.*.alt' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['sometimes', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:300'],
            'gallery_existing' => ['sometimes', 'array'],
            'gallery_existing.*.id' => ['required', 'integer'],
            'gallery_existing.*.alt' => ['nullable', 'string', 'max:255'],
            'gallery_existing.*.remove' => ['sometimes', 'boolean'],
        ];
    }

    public static function messages(): array
    {
        return [
            'title.required' => 'O título da publicação é obrigatório.',
            'title.max' => 'O título da publicação não pode ultrapassar 255 caracteres.',
            'slug.regex' => 'Use apenas letras minúsculas, números e hífens no endereço da página.',
            'slug.unique' => 'Já existe uma publicação com este endereço.',
            'slug.max' => 'O endereço da página não pode ultrapassar 160 caracteres.',
            'summary.max' => 'O resumo não pode ultrapassar 500 caracteres.',
            'type.required' => 'Selecione o tipo da publicação.',
            'type.enum' => 'Selecione um tipo de publicação válido.',
            'start_date.required' => 'A data de início do evento é obrigatória.',
            'start_date.date' => 'A data de início deve ser uma data válida.',
            'end_date.required' => 'A data de término do evento é obrigatória.',
            'end_date.date' => 'A data de término deve ser uma data válida.',
            'end_date.after_or_equal' => 'A data de término não pode ser anterior à data de início.',
            'start_time.required' => 'O horário de início do evento é obrigatório.',
            'start_time.date_format' => 'O horário de início deve estar no formato HH:MM.',
            'end_time.required' => 'O horário de término do evento é obrigatório.',
            'end_time.date_format' => 'O horário de término deve estar no formato HH:MM.',
            'end_time.after_or_equal' => 'O horário de término não pode ser anterior ao horário de início.',
            'location.required' => 'O local do evento é obrigatório.',
            'location.max' => 'O local do evento não pode ultrapassar 255 caracteres.',
            'author_id.exists' => 'Selecione um autor válido.',
            'speakers.*.id.required_without' => 'Selecione um participante cadastrado, digite o nome de um avulso ou remova a linha.',
            'speakers.*.name.max' => 'O nome do participante não pode ultrapassar 150 caracteres.',
            'speakers.*.id.exists' => 'Selecione um participante válido.',
            'speakers.*.id.distinct' => 'O mesmo participante foi adicionado mais de uma vez.',
            'speakers.*.participation.max' => 'A atuação não pode ultrapassar 100 caracteres.',
            'tag_ids.*.exists' => 'Selecione etiquetas válidas.',
            'tag_ids.*.distinct' => 'A mesma etiqueta foi selecionada mais de uma vez.',
            'link_url.url' => 'Informe um link válido, começando com http:// ou https://.',
            'link_label.max' => 'O texto do link não pode ultrapassar 80 caracteres.',
            'cover.image' => 'A capa deve ser uma imagem.',
            'cover.mimes' => 'A capa deve estar em JPG, PNG ou WEBP.',
            'cover.max' => 'A capa não pode ultrapassar 4 MB.',
            'cover_alt.required_with' => 'Descreva a imagem de capa no texto alternativo.',
            'gallery.max' => 'Envie no máximo 30 fotos por vez.',
            'gallery.*.file.required' => 'Selecione a foto da galeria.',
            'gallery.*.file.image' => 'Cada foto da galeria deve ser uma imagem.',
            'gallery.*.file.mimes' => 'As fotos da galeria devem estar em JPG, PNG ou WEBP.',
            'gallery.*.file.max' => 'Cada foto da galeria pode ter no máximo 4 MB.',
            'gallery.*.alt.required' => 'Descreva a foto no texto alternativo.',
            'gallery.*.alt.max' => 'O texto alternativo não pode ultrapassar 255 caracteres.',
            'published_at.date' => 'Informe uma data de publicação válida.',
            'seo_title.max' => 'O título para buscadores não pode ultrapassar 255 caracteres.',
            'seo_description.max' => 'A descrição para buscadores não pode ultrapassar 300 caracteres.',
            'gallery_existing.*.alt.max' => 'O texto alternativo não pode ultrapassar 255 caracteres.',
        ];
    }
}
