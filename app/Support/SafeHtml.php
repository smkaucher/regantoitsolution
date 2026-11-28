<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Keeps the basic formatting an admin writes in an "HTML allowed" box (paragraphs, headings, bold, lists, links,
 * images, tables) and drops everything that could run code: <script>, <iframe>, forms, event handlers (onclick=…),
 * style attributes and javascript:/data: links. Run on output, so old content in the database is covered too.
 */
class SafeHtml
{
    /** Tag => attributes it may keep. */
    private const ALLOWED = [
        'p' => [], 'br' => [], 'hr' => [], 'div' => [], 'span' => [],
        'h2' => [], 'h3' => [], 'h4' => [], 'h5' => [], 'h6' => [],
        'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [], 'small' => [], 'sub' => [], 'sup' => [], 'mark' => [],
        'ul' => [], 'ol' => [], 'li' => [], 'blockquote' => [], 'code' => [], 'pre' => [],
        'table' => [], 'thead' => [], 'tbody' => [], 'tr' => [], 'th' => ['colspan', 'rowspan'], 'td' => ['colspan', 'rowspan'],
        'a' => ['href', 'title'],
        'img' => ['src', 'alt', 'width', 'height'],
    ];

    /** Removed together with everything inside them. Any other unknown tag is unwrapped (its text is kept). */
    private const DROP = ['script', 'style', 'iframe', 'frame', 'frameset', 'object', 'embed', 'applet', 'form', 'input',
        'button', 'select', 'textarea', 'option', 'svg', 'math', 'template', 'noscript', 'link', 'meta', 'base', 'head', 'title'];

    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $doc = new DOMDocument();
        $prev = libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div>' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($prev);

        $root = null;
        foreach ($doc->childNodes as $n) {
            if ($n instanceof DOMElement) {
                $root = $n;
                break;
            }
        }
        if (!$root) {
            return e(strip_tags($html));
        }

        self::walk($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return $out;
    }

    private static function walk(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child->nodeType === XML_COMMENT_NODE || $child->nodeType === XML_PI_NODE) {
                $node->removeChild($child);
                continue;
            }
            if (!$child instanceof DOMElement) {
                continue;
            }
            $tag = strtolower($child->tagName);
            if (in_array($tag, self::DROP, true)) {
                $node->removeChild($child);
                continue;
            }
            self::walk($child);
            if (!isset(self::ALLOWED[$tag])) {
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }
            foreach (iterator_to_array($child->attributes) as $attr) {
                $name = strtolower($attr->name);
                if (!in_array($name, self::ALLOWED[$tag], true)
                    || (in_array($name, ['href', 'src'], true) && !self::safeUrl($attr->value, $name === 'src'))) {
                    $child->removeAttribute($attr->name);
                }
            }
            if ($tag === 'a' && $child->hasAttribute('href') && preg_match('#^https?://#i', $child->getAttribute('href'))) {
                $child->setAttribute('target', '_blank');
                $child->setAttribute('rel', 'noopener noreferrer nofollow');
            }
            if ($tag === 'img') {
                if (!$child->hasAttribute('src')) {
                    $node->removeChild($child);
                    continue;
                }
                $child->setAttribute('loading', 'lazy');
            }
        }
    }

    /** http(s), mailto:, tel:, or a link on this site. Nothing that can run code (javascript:, data:, vbscript:…). */
    private static function safeUrl(string $url, bool $image): bool
    {
        // Browsers ignore control characters and spaces inside a scheme ("java\tscript:"), so they are removed first.
        $u = strtolower(preg_replace('/[\x00-\x20\x7f]+/', '', html_entity_decode($url, ENT_QUOTES | ENT_HTML5)));
        if ($u === '' || str_starts_with($u, '//')) {
            return false;
        }
        if (preg_match('#^[a-z][a-z0-9+.-]*:#', $u)) {
            return preg_match($image ? '#^https?:#' : '#^(https?|mailto|tel):#', $u) === 1;
        }

        return true; // relative: /storage/…, #section, page.html
    }
}
