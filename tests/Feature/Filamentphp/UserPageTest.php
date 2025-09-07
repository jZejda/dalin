<?php

namespace Tests\Feature\Filamentphp;

use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(
        User::where('id', '=', 1)->first()
    );
});

test('the application returns a successful response', function () {
    $response = $this->get('/admin/users');

    $response->assertStatus(200);
});

it('can load the relation manager', function () {
    $user = User::factory()->create();

    Livewire::test(ListUsers::class)
    ->assertOk()
    ->assertCanSeeTableRecords([$user]);
});
