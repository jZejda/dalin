<?php

use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\PostResource;
use App\Filament\Resources\UserResource\RelationManagers\UserCreditRelationManager;
use App\Models\User;

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

    $this->livewire(EditUser::class, [
        'record' => $user->id,
    ])->assertSeeResource(PostResource::class);
});

