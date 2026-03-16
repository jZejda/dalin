<?php

declare(strict_types=1);

use App\Models\User;
use Random\RandomException;

beforeEach(function () {
    $this->user = User::factory()->create([
        'active' => true,
    ]);
});

test('user can set api key', function () {
    $plainApiKey = bin2hex(random_bytes(32));
    $this->user->setApiKey($plainApiKey);

    $this->user->refresh();

    expect($this->user->api_key_hash)->not->toBeNull()
        ->and($this->user->api_key_hash)->toBe(hash('sha256', $plainApiKey));
});

test('api key is properly hashed in database', function () {
    $plainApiKey = bin2hex(random_bytes(32));
    $this->user->setApiKey($plainApiKey);

    $expectedHash = hash('sha256', $plainApiKey);

    $this->user->refresh();
    expect($this->user->api_key_hash)->toBe($expectedHash);
});

test('different api keys generate different hashes', function () {
    $firstApiKey = bin2hex(random_bytes(32));
    $this->user->setApiKey($firstApiKey);
    $firstHash = $this->user->api_key_hash;

    $secondApiKey = bin2hex(random_bytes(32));
    $this->user->setApiKey($secondApiKey);

    $this->user->refresh();

    expect($this->user->api_key_hash)->not->toBe($firstHash)
        ->and($this->user->api_key_hash)->toBe(hash('sha256', $secondApiKey));
});

test(/**
 * @throws Throwable
 * @throws RandomException
 */ 'user can delete api key by setting it to null', function () {
    $apiKey = bin2hex(random_bytes(32));
    $this->user->setApiKey($apiKey);

    expect($this->user->api_key_hash)->not->toBeNull();

    $this->user->api_key_hash = null;
    $this->user->saveOrFail();

    $this->user->refresh();
    expect($this->user->api_key_hash)->toBeNull();
});

test(/**
 * @throws RandomException
 */ 'api key hash is hidden from array output', function () {
    $plainApiKey = bin2hex(random_bytes(32));
    $this->user->setApiKey($plainApiKey);

    $userArray = $this->user->toArray();

    expect($userArray)->not->toHaveKey('api_key_hash');
});

test(/**
 * @throws RandomException
 */ 'generated api key has correct length', function () {
    $plainApiKey = bin2hex(random_bytes(32));

    expect(strlen($plainApiKey))->toBe(64); // 32 bytes = 64 hex characters
});
