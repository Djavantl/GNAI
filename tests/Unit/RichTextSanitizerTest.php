<?php

namespace Tests\Unit;

use App\Support\RichTextSanitizer;
use PHPUnit\Framework\TestCase;

class RichTextSanitizerTest extends TestCase
{
    public function test_it_preserves_ck_editor_markup(): void
    {
        $html = '<h2>Titulo</h2><p><strong>Negrito</strong> e <em>italico</em></p><ul><li>Item</li></ul>';

        $this->assertSame($html, RichTextSanitizer::sanitize($html));
    }

    public function test_it_removes_xss_payloads_without_removing_allowed_markup(): void
    {
        $html = '<p onclick="alert(1)">Texto <strong>ok</strong></p>'
            . '<script>alert(1)</script>'
            . '<a href="javascript:alert(1)" target="_blank">link</a>'
            . '<img src=x onerror="alert(1)">';

        $sanitized = RichTextSanitizer::sanitize($html);

        $this->assertSame(
            '<p>Texto <strong>ok</strong></p><a target="_blank" rel="noopener noreferrer">link</a>',
            $sanitized
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

        $this->assertSame([
            'name' => 'A & B',
            'description' => '<p>Texto <a href="https://example.com">seguro</a></p>',
            'nested' => [
                'notes' => '<p>Ok</p>',
            ],
        ], RichTextSanitizer::sanitizeMixed($data));
    }
}
