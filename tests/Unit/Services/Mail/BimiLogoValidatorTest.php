<?php

declare(strict_types=1);

use App\Services\Mail\BimiLogoValidator;

function bimiSvg(string $body = '<circle cx="50" cy="50" r="40" fill="#1f2a1f"/>', string $attributes = 'version="1.2" baseProfile="tiny-ps" viewBox="0 0 100 100"', string $title = '<title>ABM</title>'): string
{
    return '<?xml version="1.0" encoding="UTF-8"?>'
        .'<svg xmlns="http://www.w3.org/2000/svg" '.$attributes.'>'.$title.$body.'</svg>';
}

it('accepts an SVG Tiny PS logo', function (): void {
    expect((new BimiLogoValidator)->errors(bimiSvg()))->toBe([]);
});

it('accepts internal references and inline styles', function (): void {
    $body = '<defs><linearGradient id="g"><stop offset="0" stop-color="#fff"/></linearGradient></defs>'
        .'<rect width="100" height="100" fill="url(#g)" style="stroke:#000"/>';

    expect((new BimiLogoValidator)->isValid(bimiSvg($body)))->toBeTrue();
});

it('rejects an invalid BIMI logo', function (string $svg, string $error): void {
    expect((new BimiLogoValidator)->errors($svg))->toContain($error);
})->with([
    'not XML' => ['<svg', 'not_svg'],
    'not an SVG' => ['<html xmlns="http://www.w3.org/1999/xhtml"/>', 'not_svg'],
    'doctype' => ['<!DOCTYPE svg [<!ENTITY x "y">]>'.bimiSvg(), 'doctype'],
    'too large' => [bimiSvg(str_repeat('<g/>', 9000)), 'too_large'],
    'missing profile' => [bimiSvg(attributes: 'viewBox="0 0 100 100"'), 'profile'],
    'position' => [bimiSvg(attributes: 'version="1.2" baseProfile="tiny-ps" viewBox="0 0 100 100" x="0" y="0"'), 'position'],
    'not square' => [bimiSvg(attributes: 'version="1.2" baseProfile="tiny-ps" viewBox="0 0 200 100"'), 'view_box'],
    'missing viewBox' => [bimiSvg(attributes: 'version="1.2" baseProfile="tiny-ps"'), 'view_box'],
    'missing title' => [bimiSvg(title: ''), 'title'],
    'script' => [bimiSvg('<script>alert(1)</script>'), 'unsafe'],
    'event handler' => [bimiSvg('<rect width="1" height="1" onclick="alert(1)"/>'), 'unsafe'],
    'external image' => [bimiSvg('<image href="https://example.com/a.png"/>'), 'unsafe'],
    'external use' => [bimiSvg('<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="https://example.com/a.svg#x"/>'), 'unsafe'],
    'external url' => [bimiSvg('<rect width="1" height="1" fill="url(https://example.com/a)"/>'), 'unsafe'],
    'style import' => [bimiSvg('<style>@import "https://example.com/a.css";</style>'), 'unsafe'],
    'foreignObject' => [bimiSvg('<foreignObject/>'), 'unsafe'],
]);
