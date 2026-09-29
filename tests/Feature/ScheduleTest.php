<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

// schedule:run is triggered over HTTP on shared hosting, where spawning `php artisan …`
// fails (exit 126) — every due task has to run in-process.
test('no scheduled task spawns an artisan subprocess', function (): void {
    $this->artisan('schedule:list')->assertSuccessful();

    $runnable = collect(app(Schedule::class)->events())
        ->filter(fn (Event $event): bool => $event->filtersPass(app()));

    expect($runnable)->not->toBeEmpty()
        ->and($runnable->filter(fn (Event $event): bool => str_contains((string) $event->command, 'artisan')))->toBeEmpty();
});

test('filament-excel prune is scheduled in-process', function (): void {
    $this->artisan('schedule:list')->assertSuccessful();

    $prune = collect(app(Schedule::class)->events())
        ->first(fn (Event $event): bool => $event->description === 'filament-excel:prune' && $event->command === null);

    expect($prune)->not->toBeNull()
        ->and($prune->filtersPass(app()))->toBeTrue();
});
