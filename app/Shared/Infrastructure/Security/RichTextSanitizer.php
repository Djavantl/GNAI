<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

final class RichTextSanitizer
{
    private const ALLOWED_TAGS = [
        'a', 'b', 'blockquote', 'br', 'caption', 'code', 'col', 'colgroup',
        'del', 'div', 'em', 'font', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'hr', 'i', 'img', 'li', 'ol', 'p', 'pre', 's', 'span', 'strike',
        'strong', 'sub', 'sup', 'table', 'tbody', 'td', 'tfoot', 'th',
        'thead', 'tr', 'u', 'ul',
    ];

    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'rel', 'target', 'title'],
        'td' => ['colspan', 'rowspan'],
        'th' => ['colspan', 'rowspan', 'scope'],
        'img' => ['src', 'alt', 'width', 'height', 'class', 'data-filename'],
        'font' => ['face', 'color', 'size'],
        'table' => ['class', 'width', 'cellpadding', 'cellspacing', 'border'],
        'col' => ['span', 'width'],
        'colgroup' => ['span'],
        'ol' => ['start', 'type'],
        'li' => ['value'],
    ];

    private const GLOBAL_ATTRIBUTES = ['style', 'title', 'dir', 'lang'];

    private const ALLOWED_CLASSES = [
        'img' => ['img-rounded', 'img-circle', 'img-thumbnail', 'pull-left', 'pull-right'],
        'table' => ['table', 'table-bordered', 'table-striped', 'table-condensed', 'table-hover'],
    ];

    private const DROP_WITH_CONTENT = ['iframe', 'object', 'script', 'style', 'svg', 'math', 'template'];

    public static function sanitizeMixed(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(static fn (mixed $item): mixed => self::sanitizeMixed($item), $value);
        }

        if (! is_string($value) || ! self::containsHtmlTag($value)) {
            return $value;
        }

        return self::sanitize($value);
    }

    public static function sanitize(string $html, bool $decodeEntities = true): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');

        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>',
            LIBXML_HTML_NODEFDTD | LIBXML_NONET
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementsByTagName('body')->item(0);

        if (! $root instanceof DOMElement) {
            return '';
        }

        self::cleanNode($root);

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        // Mantém a assinatura por compatibilidade. Nunca decodifique entidades após
        // sanitizar: isso pode transformar texto escapado em markup executável.
        return trim($output);
    }

    public static function toPlainText(string $html): string
    {
        $sanitized = self::containsHtmlTag($html)
            ? self::sanitize($html)
            : $html;

        $withSeparators = preg_replace(
            '/<br\s*\/?>|<\/(p|div|li|h[1-6]|tr)>/i',
            ' ',
            $sanitized,
        ) ?? $sanitized;

        $plainText = html_entity_decode(
            strip_tags($withSeparators),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8',
        );

        return trim(preg_replace('/\s+/u', ' ', $plainText) ?? $plainText);
    }

    private static function containsHtmlTag(string $value): bool
    {
        return preg_match('/<\/?[a-z][\s\S]*>/i', $value) === 1;
    }

    private static function cleanNode(DOMNode $node): void
    {
        for ($child = $node->lastChild; $child !== null; $child = $previous) {
            $previous = $child->previousSibling;

            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);

                if (in_array($tag, self::DROP_WITH_CONTENT, true)) {
                    $child->parentNode?->removeChild($child);

                    continue;
                }

                self::cleanNode($child);

                if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                    self::unwrapNode($child);

                    continue;
                }

                self::cleanAttributes($child, $tag);

                if ($tag === 'img' && ! $child->hasAttribute('src')) {
                    $child->parentNode?->removeChild($child);
                }
            } elseif (! $child instanceof DOMText) {
                // Comentários e instruções de processamento não são conteúdo do editor.
                $child->parentNode?->removeChild($child);
            }
        }
    }

    private static function unwrapNode(DOMElement $node): void
    {
        $parent = $node->parentNode;

        if (! $parent) {
            return;
        }

        while ($node->firstChild) {
            $parent->insertBefore($node->firstChild, $node);
        }

        $parent->removeChild($node);
    }

    private static function cleanAttributes(DOMElement $node, string $tag): void
    {
        for ($i = $node->attributes->length - 1; $i >= 0; $i--) {
            $attribute = $node->attributes->item($i);

            if (! $attribute) {
                continue;
            }

            $name = strtolower($attribute->name);
            $allowed = array_merge(self::GLOBAL_ATTRIBUTES, self::ALLOWED_ATTRIBUTES[$tag] ?? []);

            if (! in_array($name, $allowed, true)) {
                $node->removeAttribute($attribute->name);

                continue;
            }

            if ($name === 'style') {
                $style = RichTextStyleSanitizer::sanitize($attribute->value);

                if ($style === '') {
                    $node->removeAttribute('style');
                } else {
                    $node->setAttribute('style', $style);
                }
            }

            if ($name === 'class') {
                $classes = array_intersect(
                    preg_split('/\s+/', trim($attribute->value)),
                    self::ALLOWED_CLASSES[$tag] ?? [],
                );

                if ($classes === []) {
                    $node->removeAttribute('class');
                } else {
                    $node->setAttribute('class', implode(' ', array_unique($classes)));
                }
            }

            if ($name === 'dir' && ! in_array($attribute->value, ['ltr', 'rtl', 'auto'], true)) {
                $node->removeAttribute($name);
            }

            if ($name === 'lang' && ! preg_match('/^[a-z]{2,8}(?:-[a-z0-9]{1,8})*$/iD', $attribute->value)) {
                $node->removeAttribute($name);
            }

            if ($tag === 'img' && $name === 'src' && ! self::isSafeImage($attribute->value)) {
                $node->removeAttribute($name);
            }

            if ($name === 'width' || $name === 'height') {
                if (! preg_match('/^(?:\d{1,5}(?:\.\d+)?)(?:%|px)?$/D', $attribute->value)) {
                    $node->removeAttribute($name);
                }
            }

            if (in_array($name, ['cellpadding', 'cellspacing', 'border'], true)) {
                if (! ctype_digit($attribute->value) || (int) $attribute->value > 100) {
                    $node->removeAttribute($name);
                }
            }

            if ($tag === 'font') {
                $valid = match ($name) {
                    'face' => RichTextStyleSanitizer::isFontFamily($attribute->value),
                    'color' => RichTextStyleSanitizer::isColor($attribute->value),
                    'size' => preg_match('/^[+-]?[1-7]$/D', $attribute->value) === 1,
                    default => true,
                };

                if (! $valid) {
                    $node->removeAttribute($name);
                }
            }

            if (in_array($name, ['start', 'value'], true) && ! preg_match('/^-?\d{1,6}$/D', $attribute->value)) {
                $node->removeAttribute($name);
            }

            if ($tag === 'ol' && $name === 'type' && ! in_array($attribute->value, ['1', 'a', 'A', 'i', 'I'], true)) {
                $node->removeAttribute($name);
            }

            if ($tag === 'a' && $name === 'href' && ! self::isSafeUrl($attribute->value)) {
                $node->removeAttribute($attribute->name);
            }

            if (in_array($tag, ['td', 'th'], true) && in_array($name, ['colspan', 'rowspan'], true)) {
                self::sanitizePositiveIntegerAttribute($node, $attribute->name);
            }

            if ($name === 'span' && in_array($tag, ['col', 'colgroup'], true)) {
                self::sanitizePositiveIntegerAttribute($node, $name);
            }

            if ($tag === 'a' && $name === 'target' && ! in_array($attribute->value, ['_blank', '_self'], true)) {
                $node->removeAttribute($name);
            }

            if ($tag === 'th' && $name === 'scope' && ! in_array($attribute->value, ['col', 'row', 'colgroup', 'rowgroup'], true)) {
                $node->removeAttribute($attribute->name);
            }
        }

        if ($tag === 'a' && $node->getAttribute('target') === '_blank') {
            $node->setAttribute('rel', 'noopener noreferrer');
        }
    }

    private static function sanitizePositiveIntegerAttribute(DOMElement $node, string $attribute): void
    {
        $value = $node->getAttribute($attribute);

        if (! ctype_digit($value) || (int) $value < 1 || (int) $value > 100) {
            $node->removeAttribute($attribute);
        }
    }

    private static function isSafeImage(string $url): bool
    {
        if (! str_starts_with(strtolower($url), 'data:')) {
            return self::isSafeUrl($url, image: true);
        }

        if (! preg_match('~^data:(image/(?:png|jpeg|gif|webp|bmp));base64,([a-z0-9+/=]+)$~iD', $url, $match)) {
            return false;
        }

        $bytes = base64_decode($match[2], strict: true);

        if ($bytes === false || $bytes === '') {
            return false;
        }

        $image = @getimagesizefromstring($bytes);

        return $image !== false && strtolower($match[1]) === ($image['mime'] ?? null);
    }

    private static function isSafeUrl(string $url, bool $image = false): bool
    {
        $url = trim($url);

        if (preg_match('/[\x00-\x20\x7f\\\\]/', $url)) {
            return false;
        }

        if ($url === '' || str_starts_with($url, '#')) {
            return ! $image;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);

        if ($scheme === false) {
            return false;
        }

        return $scheme === null || in_array(strtolower($scheme), $image
            ? ['http', 'https']
            : ['http', 'https', 'mailto', 'tel'], true);
    }
}
