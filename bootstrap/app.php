<?php
use Illuminate\Foundation\Application;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withExceptions(function () {})
    ->withSchedule(function (Schedule $schedule) {
        // Cada minuto: despacha publicaciones pendientes cuya hora ya llegó
        $schedule->call(function () {
            $ids = \App\Models\Publication::query()
                ->where('status', 'pending')
                ->whereNotNull('scheduled_at')
                ->where('scheduled_at', '<=', now('UTC'))
                ->limit(50)
                ->pluck('id');

            foreach ($ids as $id) {
                dispatch(new \App\Jobs\PublishPostJob($id));
            }
        })->everyMinute();
    })
    ->create();