<?php

use App\Filament\Resources\PostResource;
use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('the application returns a successful response', function () {
    $response = $this->get('/admin/dashboard');

    $response->assertStatus(200);
});

it('can render page', function () {
    $this->get(PostResource::getUrl('index'))->assertSuccessful();
});
