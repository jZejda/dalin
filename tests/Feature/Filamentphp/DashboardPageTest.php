<?php

use App\Filament\Resources\PostResource;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(
        User::where('id', '=', 1)->first()
    );
});

test('the application returns a successful response', function () {
    $response = $this->get('/admin/user-credits');

    $response->assertStatus(200);
});

test('the application returns a successful response credit', function () {
    $response = $this->get('/admin/pages');

    $response->assertStatus(200);
});


test('the application returns a successful response posts', function () {
    $response = $this->get('/admin/posts');

    $response->assertStatus(200);
});

it('can render page', function () {
    $this->get(PostResource::getUrl('index'))->assertSuccessful();
});
