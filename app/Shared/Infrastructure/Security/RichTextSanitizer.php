<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Security;

use DOMDocument;
use DOMElement;
use DOMNode;

final class RichTextSanitizer
{
    private const ALLOWED_TAGS = [
        'a', 'b', 'blockquote', 'br', 'em', 'h2', 'h3', 'h4', 'i', 'li',
        'ol', 'p', 'strong', 'table', 'tbody', 'td', 'tfoot', 'th', 'thead',
        'tr', 'ul',
    ];

    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'rel', 'target', 'title'],
        'td' => ['colspan', 'rowspan'],
        'th' => ['colspan', 'rowspan', 'scope'],
    ];

    private const DROP_WITH_CONTENT = ['iframe', 'object', 'script', 'style', 'svg'];

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

    public static function sanitize(string $html): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');

        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div>'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->documentElement;

        if (! $root instanceof DOMElement) {
            return '';
        }

        self::cleanNode($root);

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim(html_entity_decode($output, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
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
            } else {
                self::cleanNode($child);
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
            $allowed = self::ALLOWED_ATTRIBUTES[$tag] ?? [];

            if (! in_array($name, $allowed, true)) {
                $node->removeAttribute($attribute->name);

                continue;
            }

            if ($tag === 'a' && $name === 'href' && ! self::isSafeUrl($attribute->value)) {
                $node->removeAttribute($attribute->name);
            }

            if (in_array($tag, ['td', 'th'], true) && in_array($name, ['colspan', 'rowspan'], true)) {
                self::sanitizePositiveIntegerAttribute($node, $attribute->name);
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

    private static function isSafeUrl(string $url): bool
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($url === '' || str_starts_with($url, '#') || str_starts_with($url, '/')) {
            return true;
        }

        $scheme = parse_url($url, PHP_URL_SCHEME);

        return $scheme === null || in_array(strtolower($scheme), ['http', 'https', 'mailto', 'tel'], true);
    }
}
