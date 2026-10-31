<?php

declare(strict_types=1);

namespace App\Domains\Inclusion\Application\Data\Publications\Concerns;

use App\Domains\Inclusion\Domain\Enums\PublicationType;
use App\Domains\Inclusion\Domain\Models\Publication;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Support\Validation\ValidationContext;

/**
 * Regras compartilhadas entre criação e edição de publicações.
 * Datas, horários e local só são exigidos quando a publicação possui período
 * (has_schedule). Na ausência do campo, o comportamento da antiga agenda
 * institucional é preservado e o período continua obrigatório.
 */
trait PublicationRules
{
    public const IMAGE_RULES = ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'];

    /**
     * @return array<string, mixed>
     */
    protected static function publicationRules(?ValidationContext $context, int|string|null $ignoreId = null): array
    {
        $payload = $context?->payload ?? [];
        $scheduled = filter_var($payload['has_schedule'] ?? true, FILTER_VALIDATE_BOOL);
        $requiredWhenScheduled = $scheduled ? 'required' : 'nullable';

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:'.Publication::SLUG_MAX_LENGTH,
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('publications', 'slug')->ignore($ignoreId),
            ],
            'summary' => ['nullable', 'string', 'max:500'],
            'type' => ['sometimes', 'required', Rule::enum(PublicationType::class)],
            'has_schedule' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
            'start_date' => [$requiredWhenScheduled, 'date'],
            'end_date' => [$requiredWhenScheduled, 'date', 'after_or_equal:start_date'],
            'start_time' => [$requiredWhenScheduled, 'date_format:H:i'],
            'end_time' => [$requiredWhenScheduled, 'date_format:H:i', 'after_or_equal:start_time'],
            'location' => [$requiredWhenScheduled, 'string', 'max:255'],
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
            'cover' => ['nullable', ...self::IMAGE_RULES],
            'cover_alt' => ['nullable', 'required_with:cover', 'string', 'max:255'],
            'remove_cover' => ['sometimes', 'boolean'],
            'gallery' => ['sometimes', 'array', 'max:30'],
            'gallery.*.file' => ['required', ...self::IMAGE_RULES],
            'gallery.*.alt' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['sometimes', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:300'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected static function publicationMessages(): array
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
        ];
    }
}
