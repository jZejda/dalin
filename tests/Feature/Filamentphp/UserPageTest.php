<?php

declare(strict_types=1);

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use Livewire\Livewire;

test('the application returns a successful response', function () {
    actingAsSuperAdmin();

    $this->get('/admin/users')->assertStatus(200);
});

it('can load the relation manager', function () {
    actingAsSuperAdmin();

    $user = User::factory()->create();

    Livewire::test(ListUsers::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$user]);
});
