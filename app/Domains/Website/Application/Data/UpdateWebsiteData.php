<?php

declare(strict_types=1);

namespace App\Domains\Website\Application\Data;

use App\Domains\Website\Domain\Enums\WebsiteSectionType;
use App\Domains\Website\Domain\Models\WebsiteSetting;
use App\Domains\Website\Domain\Support\AboutAppearance;
use App\Domains\Website\Domain\Support\GuidanceAppearance;
use App\Domains\Website\Domain\Support\TeamAppearance;
use App\Domains\Website\Domain\Support\ContactAppearance;
use App\Domains\Website\Domain\Support\PublicationsAppearance;
use App\Domains\Website\Domain\Support\PillarCardAppearance;
use App\Domains\Website\Domain\Support\PillarsAppearance;
use App\Domains\Website\Domain\Support\HeroAppearance;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
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
        $appearanceRules = [
            'sections.*.hero.badge_text' => ['nullable', 'string', 'max:120'],
            'sections.*.slides' => ['sometimes', 'array', 'max:8'],
            'sections.*.slides.*.id' => ['required', 'string', 'max:40', 'regex:/^[A-Za-z0-9_-]+$/'],
            'sections.*.slides.*.image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'sections.*.slides.*.remove_image' => ['sometimes', 'boolean'],
            'sections.*.slides.*.alt' => ['required', 'string', 'max:255'],
            'sections.*.slides.*.url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/|\/|#|mailto:|tel:)\S*$/i'],
        ];

        foreach ([
            'sections.*.hero' => HeroAppearance::class,
            'sections.*.about' => AboutAppearance::class,
            'sections.*.pillars' => PillarsAppearance::class,
            'sections.*.guidance' => GuidanceAppearance::class,
            'sections.*.publications' => PublicationsAppearance::class,
            'sections.*.events' => PublicationsAppearance::class,
            'sections.*.team' => TeamAppearance::class,
            'sections.*.contact' => ContactAppearance::class,
            'sections.*.items.*.appearance' => PillarCardAppearance::class,
        ] as $scope => $appearanceClass) {
            $appearanceRules[$scope] = ['sometimes', 'array'];

            foreach ($appearanceClass::RANGES as $key => [$min, $max]) {
                $appearanceRules["{$scope}.{$key}"] = ['sometimes', 'integer', "min:{$min}", "max:{$max}"];
            }
            foreach ($appearanceClass::COLORS as $key) {
                $appearanceRules["{$scope}.{$key}"] = ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'];
            }
            foreach ($appearanceClass::CHOICES as $key => $options) {
                $appearanceRules["{$scope}.{$key}"] = ['sometimes', Rule::in($options)];
            }
            foreach ($appearanceClass::TOGGLES as $key => $default) {
                $appearanceRules["{$scope}.{$key}"] = ['sometimes', 'boolean'];
            }
        }

        return [
            ...$appearanceRules,
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
            'sections.*.items.*.url' => ['nullable', 'string', 'max:500', 'regex:/^(https?:\/\/|\/|#|mailto:|tel:)\S*$/i'],
            'sections.*.items.*.title' => ['nullable', 'string', 'max:120'],
            'sections.*.items.*.text' => ['nullable', 'string', 'max:300'],
        ];
    }

    public static function messages(): array
    {
        return [
            'sections.*.contact.*.regex' => 'Informe uma cor hexadecimal válida, por exemplo #322b75.',
            'sections.*.contact.*.min' => 'O valor está abaixo do mínimo permitido para este ajuste.',
            'sections.*.contact.*.max' => 'O valor ultrapassa o limite permitido para este ajuste.',
            'sections.*.contact.*.integer' => 'Informe um valor inteiro para este ajuste.',
            'sections.*.contact.*.in' => 'Selecione uma opção válida.',
            'sections.*.team.*.regex' => 'Informe uma cor hexadecimal válida, por exemplo #322b75.',
            'sections.*.team.*.min' => 'O valor está abaixo do mínimo permitido para este ajuste.',
            'sections.*.team.*.max' => 'O valor ultrapassa o limite permitido para este ajuste.',
            'sections.*.team.*.integer' => 'Informe um valor inteiro para este ajuste.',
            'sections.*.team.*.in' => 'Selecione uma opção válida.',
            'sections.*.events.*.regex' => 'Informe uma cor hexadecimal válida, por exemplo #322b75.',
            'sections.*.events.*.min' => 'O valor está abaixo do mínimo permitido para este ajuste.',
            'sections.*.events.*.max' => 'O valor ultrapassa o limite permitido para este ajuste.',
            'sections.*.events.*.integer' => 'Informe um valor inteiro para este ajuste.',
            'sections.*.events.*.in' => 'Selecione uma opção válida.',
            'sections.*.publications.*.regex' => 'Informe uma cor hexadecimal válida, por exemplo #322b75.',
            'sections.*.publications.*.min' => 'O valor está abaixo do mínimo permitido para este ajuste.',
            'sections.*.publications.*.max' => 'O valor ultrapassa o limite permitido para este ajuste.',
            'sections.*.publications.*.integer' => 'Informe um valor inteiro para este ajuste.',
            'sections.*.publications.*.in' => 'Selecione uma opção válida.',
            'sections.*.guidance.*.regex' => 'Informe uma cor hexadecimal válida, por exemplo #322b75.',
            'sections.*.guidance.*.min' => 'O valor está abaixo do mínimo permitido para este ajuste.',
            'sections.*.guidance.*.max' => 'O valor ultrapassa o limite permitido para este ajuste.',
            'sections.*.guidance.*.integer' => 'Informe um valor inteiro para este ajuste.',
            'sections.*.guidance.*.in' => 'Selecione uma opção válida.',
            'sections.*.pillars.*.regex' => 'Informe uma cor hexadecimal válida, por exemplo #322b75.',
            'sections.*.pillars.*.min' => 'O valor está abaixo do mínimo permitido para este ajuste.',
            'sections.*.pillars.*.max' => 'O valor ultrapassa o limite permitido para este ajuste.',
            'sections.*.pillars.*.integer' => 'Informe um valor inteiro para este ajuste.',
            'sections.*.pillars.*.in' => 'Selecione uma opção válida.',
            'sections.*.items.*.appearance.*.regex' => 'Informe uma cor hexadecimal válida, por exemplo #322b75.',
            'sections.*.items.*.appearance.*.min' => 'O valor está abaixo do mínimo permitido para este ajuste.',
            'sections.*.items.*.appearance.*.max' => 'O valor ultrapassa o limite permitido para este ajuste.',
            'sections.*.items.*.appearance.*.integer' => 'Informe um valor inteiro para este ajuste.',
            'sections.*.items.*.appearance.*.in' => 'Selecione uma opção válida.',
            'sections.*.about.*.regex' => 'Informe uma cor hexadecimal válida, por exemplo #322b75.',
            'sections.*.about.*.min' => 'O valor está abaixo do mínimo permitido para este ajuste.',
            'sections.*.about.*.max' => 'O valor ultrapassa o limite permitido para este ajuste.',
            'sections.*.about.*.integer' => 'Informe um valor inteiro para este ajuste.',
            'sections.*.about.*.in' => 'Selecione uma opção válida.',
            'sections.*.hero.*.regex' => 'Informe uma cor hexadecimal válida, por exemplo #322b75.',
            'sections.*.hero.*.min' => 'O valor está abaixo do mínimo permitido para este ajuste.',
            'sections.*.hero.*.max' => 'O valor ultrapassa o limite permitido para este ajuste.',
            'sections.*.hero.*.integer' => 'Informe um valor inteiro para este ajuste.',
            'sections.*.hero.*.in' => 'Selecione uma opção válida.',
            'sections.*.hero.badge_text.max' => 'O texto da identificação pode ter até 120 caracteres.',
            'sections.*.slides.max' => 'Adicione no máximo 8 imagens ao banner.',
            'sections.*.slides.*.image.image' => 'Selecione uma imagem válida.',
            'sections.*.slides.*.image.mimes' => 'Use uma imagem JPG, PNG ou WEBP.',
            'sections.*.slides.*.image.max' => 'Cada imagem pode ter até 4 MB.',
            'sections.*.slides.*.alt.required' => 'Descreva o conteúdo desta imagem para quem usa leitor de tela.',
            'sections.*.slides.*.alt.max' => 'A descrição pode ter até 255 caracteres.',
            'sections.*.slides.*.url.regex' => 'Use um link iniciado por http://, https://, /, #, mailto: ou tel:.',
            'sections.*.slides.*.url.max' => 'O link pode ter até 500 caracteres.',
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
            'sections.*.button_url.regex' => 'Use um link iniciado por http://, https://, /, #, mailto: ou tel:.',
            'sections.*.image.image' => 'A imagem da seção deve ser uma imagem.',
            'sections.*.image.mimes' => 'A imagem da seção deve estar em JPG, PNG ou WEBP.',
            'sections.*.image.max' => 'A imagem da seção não pode ultrapassar 4 MB.',
            'sections.*.image_alt.max' => 'O texto alternativo não pode ultrapassar 255 caracteres.',
            'sections.*.limit.min' => 'Exiba pelo menos 1 item.',
            'sections.*.limit.max' => 'Exiba no máximo 12 itens.',
            'sections.*.items.max' => 'Cada seção pode ter no máximo 12 itens.',
            'sections.*.items.*.icon.regex' => 'Use uma classe de ícone válida, por exemplo "fa fa-star".',
            'sections.*.items.*.url.max' => 'O link do documento não pode ultrapassar 500 caracteres.',
            'sections.*.items.*.url.regex' => 'Use um link iniciado por http://, https://, /, #, mailto: ou tel:.',
            'sections.*.items.*.title.max' => 'O título do item não pode ultrapassar 120 caracteres.',
            'sections.*.items.*.text.max' => 'O texto do item não pode ultrapassar 300 caracteres.',
        ];
    }

    public static function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $sections = $validator->getData()['sections'] ?? [];
            $previousSections = collect(WebsiteSetting::current()->sections ?? [])->keyBy('id');

            foreach ($sections as $index => $section) {
                if ($section['type'] !== WebsiteSectionType::HERO->value) {
                    continue;
                }

                $previous = $previousSections->get($section['id']);
                $previous = ($previous['type'] ?? null) === 'hero' ? $previous : [];
                $previousSlides = array_column($previous['slides'] ?? [], null, 'id');
                $slides = $section['slides'] ?? [];
                $seen = [];

                foreach ($slides as $slideIndex => $slide) {
                    $id = (string) $slide['id'];
                    $field = "sections.{$index}.slides.{$slideIndex}.image";

                    if (isset($seen[$id])) {
                        $validator->errors()->add($field, 'Cada imagem deve ter uma identificação diferente.');
                    }
                    $seen[$id] = true;

                    $keepsImage = ! filter_var($slide['remove_image'] ?? false, FILTER_VALIDATE_BOOL)
                        && filled($previousSlides[$id]['image_path'] ?? null);

                    if (! $keepsImage && ! (($slide['image'] ?? null) instanceof UploadedFile)) {
                        $validator->errors()->add($field, 'Selecione uma imagem ou remova este item da lista.');
                    }
                }

                if (($section['hero']['mode'] ?? 'text') !== 'banners' || $slides !== []) {
                    continue;
                }

                $keepsBackground = ! filter_var($section['remove_image'] ?? false, FILTER_VALIDATE_BOOL)
                    && filled($previous['image_path'] ?? null);

                if (! $keepsBackground && ! (($section['image'] ?? null) instanceof UploadedFile)) {
                    $validator->errors()->add(
                        "sections.{$index}.slides",
                        'Adicione pelo menos uma imagem para o modo de banners.',
                    );
                } elseif (blank($section['image_alt'] ?? null)) {
                    $validator->errors()->add(
                        "sections.{$index}.image_alt",
                        'Descreva o conteúdo do banner para quem usa leitor de tela.',
                    );
                }
            }
        });
    }
}
