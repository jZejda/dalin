<?php

use App\Models\User;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('the application returns a successful response', function () {
    $response = $this->get('admin/login');

    $response->assertStatus(302);
});
