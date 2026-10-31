<?php

declare(strict_types=1);

namespace App\Domains\Website\Application\Data;

use App\Domains\Website\Domain\Enums\WebsiteSectionType;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MergeValidationRules;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
#[MergeValidationRules]
final class UpdateWebsiteData extends Data
{
    /**
     * @param  list<array<string, mixed>>  $sections
     */
    public function __construct(
        public string $siteName,
        public string $primaryColor,
        public ?string $tagline = null,
        public ?UploadedFile $logo = null,
        public bool $removeLogo = false,
        public ?string $contactEmail = null,
        public ?string $contactPhone = null,
        public ?string $address = null,
        public ?string $instagramUrl = null,
        public ?string $facebookUrl = null,
        public ?string $youtubeUrl = null,
        public ?string $websiteUrl = null,
        public ?string $footerText = null,
        public array $sections = [],
    ) {}

    public static function rules(): array
    {
        return [
            'site_name' => ['required', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'primary_color' => ['required', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_logo' => ['sometimes', 'boolean'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:40'],
            'address' => ['nullable', 'string', 'max:255'],
            'instagram_url' => ['nullable', 'url:http,https', 'max:255'],
            'facebook_url' => ['nullable', 'url:http,https', 'max:255'],
            'youtube_url' => ['nullable', 'url:http,https', 'max:255'],
            'website_url' => ['nullable', 'url:http,https', 'max:255'],
            'footer_text' => ['nullable', 'string', 'max:500'],
            'sections' => ['sometimes', 'array', 'max:20'],
            'sections.*.id' => ['required', 'string', 'max:40', 'regex:/^[A-Za-z0-9_-]+$/', 'distinct'],
            'sections.*.type' => ['required', Rule::enum(WebsiteSectionType::class)],
            'sections.*.enabled' => ['sometimes', 'boolean'],
            'sections.*.title' => ['nullable', 'string', 'max:160'],
            'sections.*.subtitle' => ['nullable', 'string', 'max:500'],
            'sections.*.body' => ['nullable', 'string', 'max:20000'],
            'sections.*.button_label' => ['nullable', 'string', 'max:60'],
            'sections.*.button_url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/|\/|#|mailto:|tel:)\S*$/i'],
            'sections.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sections.*.image_path' => ['nullable', 'string', 'max:255'],
            'sections.*.image_alt' => ['nullable', 'string', 'max:255'],
            'sections.*.remove_image' => ['sometimes', 'boolean'],
            'sections.*.limit' => ['nullable', 'integer', 'min:1', 'max:12'],
            'sections.*.items' => ['sometimes', 'array', 'max:12'],
            'sections.*.items.*.icon' => ['nullable', 'string', 'max:60', 'regex:/^[a-z0-9 -]*$/'],
            'sections.*.items.*.value' => ['nullable', 'string', 'max:30'],
            'sections.*.items.*.title' => ['nullable', 'string', 'max:120'],
            'sections.*.items.*.text' => ['nullable', 'string', 'max:300'],
        ];
    }

    public static function messages(): array
    {
        return [
            'site_name.required' => 'Informe o nome do site.',
            'site_name.max' => 'O nome do site não pode ultrapassar 120 caracteres.',
            'tagline.max' => 'O slogan não pode ultrapassar 255 caracteres.',
            'primary_color.required' => 'Escolha a cor principal do site.',
            'primary_color.regex' => 'Informe a cor no formato hexadecimal, por exemplo #322b75.',
            'logo.image' => 'A logo deve ser uma imagem.',
            'logo.mimes' => 'A logo deve estar em JPG, PNG ou WEBP.',
            'logo.max' => 'A logo não pode ultrapassar 4 MB.',
            'contact_email.email' => 'Informe um e-mail de contato válido.',
            'instagram_url.url' => 'Informe um link válido, começando com http:// ou https://.',
            'facebook_url.url' => 'Informe um link válido, começando com http:// ou https://.',
            'youtube_url.url' => 'Informe um link válido, começando com http:// ou https://.',
            'website_url.url' => 'Informe um link válido, começando com http:// ou https://.',
            'footer_text.max' => 'O texto do rodapé não pode ultrapassar 500 caracteres.',
            'sections.max' => 'A página inicial pode ter no máximo 20 seções.',
            'sections.*.type.enum' => 'Tipo de seção inválido.',
            'sections.*.title.max' => 'O título da seção não pode ultrapassar 160 caracteres.',
            'sections.*.subtitle.max' => 'O subtítulo da seção não pode ultrapassar 500 caracteres.',
            'sections.*.button_label.max' => 'O texto do botão não pode ultrapassar 60 caracteres.',
            'sections.*.button_url.regex' => 'Use um link iniciado por http://, https:// ou /.',
            'sections.*.image.image' => 'A imagem da seção deve ser uma imagem.',
            'sections.*.image.mimes' => 'A imagem da seção deve estar em JPG, PNG ou WEBP.',
            'sections.*.image.max' => 'A imagem da seção não pode ultrapassar 4 MB.',
            'sections.*.image_alt.max' => 'O texto alternativo não pode ultrapassar 255 caracteres.',
            'sections.*.limit.min' => 'Exiba pelo menos 1 item.',
            'sections.*.limit.max' => 'Exiba no máximo 12 itens.',
            'sections.*.items.max' => 'Cada seção pode ter no máximo 12 itens.',
            'sections.*.items.*.icon.regex' => 'Use uma classe de ícone válida, por exemplo "fa fa-star".',
            'sections.*.items.*.value.max' => 'O destaque não pode ultrapassar 30 caracteres.',
            'sections.*.items.*.title.max' => 'O título do item não pode ultrapassar 120 caracteres.',
            'sections.*.items.*.text.max' => 'O texto do item não pode ultrapassar 300 caracteres.',
        ];
    }
}
