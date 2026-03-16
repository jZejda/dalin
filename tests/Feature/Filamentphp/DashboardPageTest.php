<?php

declare(strict_types=1);

use App\Filament\Resources\Posts\PostResource;

test('the application returns a successful response', function () {
    actingAsSuperAdmin();

    $this->get('/admin/user-credits')->assertStatus(200);
});

test('the application returns a successful response credit', function () {
    actingAsSuperAdmin();

    $this->get('/admin/pages')->assertStatus(200);
});

test('the application returns a successful response posts', function () {
    actingAsSuperAdmin();

    $this->get('/admin/posts')->assertStatus(200);
});

it('can render page', function () {
    actingAsSuperAdmin();

    $this->get(PostResource::getUrl('index'))->assertSuccessful();
});
