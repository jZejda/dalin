<?php

declare(strict_types=1);

namespace App\Services\Mail;

use DOMAttr;
use DOMDocument;
use DOMElement;

/**
 * Checks that an SVG follows the SVG Tiny Portable/Secure profile required by BIMI
 * (the e-mail sender logo published through a `default._bimi` DNS record).
 *
 * The rules are also what makes the file safe to serve from our own origin:
 * no scripts, event handlers, external references or DTDs.
 */
final class BimiLogoValidator
{
    /** BIMI recommends keeping the logo under 32 kB. */
    public const int MAX_BYTES = 32 * 1024;

    private const string SVG_NAMESPACE = 'http://www.w3.org/2000/svg';

    private const array FORBIDDEN_ELEMENTS = [
        'script', 'foreignobject', 'image', 'a', 'animate', 'animatemotion',
        'animatetransform', 'animatecolor', 'set', 'iframe', 'video', 'audio',
    ];

    /**
     * @return list<string> Codes of the failed rules; empty when the logo is valid.
     */
    public function errors(string $svg): array
    {
        if (strlen($svg) > self::MAX_BYTES) {
            return ['too_large'];
        }

        if (preg_match('/<!(DOCTYPE|ENTITY)/i', $svg) === 1) {
            return ['doctype'];
        }

        $document = new DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadXML($svg, LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->documentElement;

        if (! $loaded || $root === null || $root->localName !== 'svg' || $root->namespaceURI !== self::SVG_NAMESPACE) {
            return ['not_svg'];
        }

        $errors = [];

        if ($root->getAttribute('version') !== '1.2' || $root->getAttribute('baseProfile') !== 'tiny-ps') {
            $errors[] = 'profile';
        }

        if ($root->hasAttribute('x') || $root->hasAttribute('y')) {
            $errors[] = 'position';
        }

        if (! $this->hasSquareViewBox($root)) {
            $errors[] = 'view_box';
        }

        if (! $this->hasTitle($root)) {
            $errors[] = 'title';
        }

        if (! $this->hasOnlySafeContent($root)) {
            $errors[] = 'unsafe';
        }

        return $errors;
    }

    public function isValid(string $svg): bool
    {
        return $this->errors($svg) === [];
    }

    private function hasSquareViewBox(DOMElement $root): bool
    {
        $parts = preg_split('/[\s,]+/', trim($root->getAttribute('viewBox')));

        if ($parts === false || count($parts) !== 4 || ! array_reduce($parts, static fn (bool $ok, string $part): bool => $ok && is_numeric($part), true)) {
            return false;
        }

        return (float) $parts[2] > 0 && (float) $parts[2] === (float) $parts[3];
    }

    private function hasTitle(DOMElement $root): bool
    {
        foreach ($root->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName === 'title' && trim($child->textContent) !== '') {
                return true;
            }
        }

        return false;
    }

    private function hasOnlySafeContent(DOMElement $root): bool
    {
        foreach ($root->getElementsByTagName('*') as $element) {
            $name = strtolower($element->localName ?? '');

            if (in_array($name, self::FORBIDDEN_ELEMENTS, true)) {
                return false;
            }

            if ($name === 'style' && $this->referencesExternalResource($element->textContent)) {
                return false;
            }
        }

        $elements = [$root, ...iterator_to_array($root->getElementsByTagName('*'))];

        foreach ($elements as $element) {
            /** @var DOMAttr $attribute */
            foreach ($element->attributes ?? [] as $attribute) {
                $name = strtolower($attribute->localName ?? '');
                $value = trim($attribute->value);

                if (str_starts_with($name, 'on')) {
                    return false;
                }

                if ($name === 'href' && ! str_starts_with($value, '#')) {
                    return false;
                }

                if ($this->referencesExternalResource($value)) {
                    return false;
                }
            }
        }

        return true;
    }

    private function referencesExternalResource(string $value): bool
    {
        return preg_match('/url\(\s*[\'"]?(?!#)/i', $value) === 1
            || stripos($value, '@import') !== false
            || stripos($value, 'javascript:') !== false;
    }
}
