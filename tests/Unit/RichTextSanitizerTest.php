<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Shared\Infrastructure\Security\RichTextSanitizer;
use DOMDocument;
use PHPUnit\Framework\TestCase;

final class RichTextSanitizerTest extends TestCase
{
    public function test_it_preserves_ck_editor_markup(): void
    {
        $html = '<h2>Titulo</h2><p><strong>Negrito</strong> e <em>italico</em></p><ul><li>Item</li></ul>';

        self::assertSame($html, RichTextSanitizer::sanitize($html));
    }

    public function test_it_removes_xss_payloads_without_removing_allowed_markup(): void
    {
        $html = '<p onclick="alert(1)">Texto <strong>ok</strong></p>'
            .'<script>alert(1)</script>'
            .'<a href="javascript:alert(1)" target="_blank">link</a>'
            .'<img src=x onerror="alert(1)">';

        $sanitized = RichTextSanitizer::sanitize($html);

        self::assertSame(
            '<p>Texto <strong>ok</strong></p><a target="_blank" rel="noopener noreferrer">link</a><img src="x">',
            $sanitized
        );
    }

    public function test_it_preserves_safe_table_markup(): void
    {
        $html = '<table><thead><tr><th scope="col">Recurso</th><th scope="col">Qtd</th></tr></thead>'
            .'<tbody><tr><td rowspan="2">Linha 1</td><td colspan="2">Valor</td></tr></tbody></table>';

        self::assertSame($html, RichTextSanitizer::sanitize($html));
    }

    public function test_it_sanitizes_unsafe_table_attributes(): void
    {
        $html = '<table style="width:100%" onclick="alert(1)"><tr><th scope="bad" colspan="0">A</th>'
            .'<td rowspan="999" class="x">B</td></tr></table>';

        self::assertSame(
            '<table style="width: 100%"><tr><th>A</th><td>B</td></tr></table>',
            RichTextSanitizer::sanitize($html)
        );
    }

    public function test_it_only_sanitizes_strings_that_contain_html_tags(): void
    {
        $data = [
            'name' => 'A & B',
            'description' => '<p>Texto <a href="https://example.com">seguro</a></p>',
            'nested' => [
                'notes' => '<svg><script>alert(1)</script></svg><p>Ok</p>',
            ],
        ];

        self::assertSame([
            'name' => 'A & B',
            'description' => '<p>Texto <a href="https://example.com">seguro</a></p>',
            'nested' => [
                'notes' => '<p>Ok</p>',
            ],
        ], RichTextSanitizer::sanitizeMixed($data));
    }

    public function test_it_preserves_summernote_inline_formatting(): void
    {
        $html = '<h1>Título</h1><p style="text-align: center; margin-left: 25px; line-height: 1.8">'
            .'<span style="font-weight: bold; font-style: italic; text-decoration: underline; font-size: 24px; font-family: Arial; color: rgb(255, 0, 0); background-color: #ffff00">Download</span>'
            .'<u>Sublinhado</u><strike>Tachado</strike><sub>2</sub><sup>3</sup></p>'
            .'<pre>Trecho de código</pre><hr>';

        self::assertSame($html, RichTextSanitizer::sanitize($html));
        self::assertSame($html, RichTextSanitizer::sanitize(RichTextSanitizer::sanitize($html)));
    }

    public function test_it_preserves_legacy_font_markup_and_safe_image_controls(): void
    {
        $html = '<font face="Arial" color="#322b75" size="4">Texto</font>'
            .'<img src="/images/logo.png" alt="Gnai" width="120" class="img-rounded pull-left" style="width: 50%; float: left">'
            .'<table class="table table-bordered"><tr><td>Conteúdo</td></tr></table>';

        self::assertSame($html, RichTextSanitizer::sanitize($html));
    }

    public function test_it_preserves_raster_images_but_rejects_svg_and_mislabeled_data(): void
    {
        $png = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jk9sAAAAASUVORK5CYII=';
        self::assertSame('<img src="'.$png.'">', RichTextSanitizer::sanitize('<img src="'.$png.'">'));
        self::assertSame('', RichTextSanitizer::sanitize('<img src="data:image/svg+xml;base64,'.base64_encode('<svg onload="alert(1)"></svg>').'">'));
        self::assertSame('', RichTextSanitizer::sanitize('<img src="data:image/png;base64,'.base64_encode('<script>alert(1)</script>').'">'));
    }

    public function test_it_removes_executable_styles_and_unrelated_classes(): void
    {
        $html = '<span style="font-weight:bold; color:expression(alert(1)); background-image:url(javascript:alert(1)); position:fixed; z-index:99999">Texto</span>'
            .'<img src="javascript:alert(1)" onerror="alert(1)">'
            .'<img src="/images/logo.png" class="img-circle summernoteOverlay button" onload="alert(1)">'
            .'<iframe src="https://example.com"></iframe>';

        self::assertSame(
            '<span style="font-weight: bold">Texto</span><img src="/images/logo.png" class="img-circle">',
            RichTextSanitizer::sanitize($html),
        );
    }

    public function test_it_does_not_turn_escaped_markup_into_executable_html(): void
    {
        $html = '<p>&lt;img src=x onerror=alert(1)&gt; &amp; texto</p>'
            .'<a href="https://example.com/?q=&quot;teste&quot;">Link</a>';

        $sanitized = RichTextSanitizer::sanitize($html);

        self::assertStringStartsWith('<p>&lt;img src=x onerror=alert(1)&gt; &amp; texto</p><a href=', $sanitized);
        self::assertStringNotContainsString('<img', $sanitized);
        self::assertSame($sanitized, RichTextSanitizer::sanitize($html, decodeEntities: false));
        self::assertSame($sanitized, RichTextSanitizer::sanitize($sanitized));
        self::assertSame('https://example.com/?q="teste"', $this->firstLinkHref($sanitized));
    }

    public function test_it_rejects_obfuscated_urls_and_css(): void
    {
        $html = '<a href="jav&#x09;ascript:alert(1)">Link</a>'
            .'<span style="color: e\\78pression(alert(1)); width: calc(100%); font-family: Arial, url(evil); font-weight: 700">Texto</span>';

        self::assertSame(
            '<a>Link</a><span style="font-weight: 700">Texto</span>',
            RichTextSanitizer::sanitize($html),
        );
    }

    private function firstLinkHref(string $html): string
    {
        $document = new DOMDocument;
        $document->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED);

        return $document->getElementsByTagName('a')->item(0)?->getAttribute('href') ?? '';
    }
}
