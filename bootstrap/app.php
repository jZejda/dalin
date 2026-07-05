<?php

use App\Jobs\SendNewPostsEmailJob;
use App\Jobs\SendSportEventEntryEndingEmailJob;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(
            headers: Request::HEADER_X_FORWARDED_FOR |
                Request::HEADER_X_FORWARDED_HOST |
                Request::HEADER_X_FORWARDED_PORT |
                Request::HEADER_X_FORWARDED_PROTO |
                Request::HEADER_X_FORWARDED_AWS_ELB,
        );

        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo('/dashboard');

        // Attribute mails dispatched during a web request to the logged-in user
        $middleware->web(append: [
            \App\Http\Middleware\CaptureMailSource::class,
        ]);

        $middleware->alias([
            'apikey' => \App\Http\Middleware\ApiKeyAuth::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->call(fn () => \Illuminate\Support\Facades\Artisan::call('queue:work', ['--stop-when-empty' => true]))->everyFiveMinutes()->name('queue:work');
        $schedule->job(new SendNewPostsEmailJob())->everyThirtyMinutes();
        $schedule->job(new SendSportEventEntryEndingEmailJob())->hourly();
        $schedule->command('marketplace:close-expired')->everyFifteenMinutes();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    })
    ->create();
