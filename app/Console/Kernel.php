<?php
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Publication;
use App\Jobs\PublishPostJob;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $event = $schedule->call(function () {
            Publication::where('mode', 'scheduled')
                ->where('status', 'pending')
                ->where('scheduled_at', '<=', now('UTC'))
                ->chunkById(100, function ($rows) {
                    foreach ($rows as $p) {
                        $p->update(['status' => 'queued']);
                        PublishPostJob::dispatch($p->id);
                    }
                });
        });

        $event->everyMinute();
        $event->withoutOverlapping();   // o ->withoutOverlapping(5)
        $event->onOneServer();
        $event->runInBackground();
        // Usa name() si tu versión lo soporta; si no, usa description():
        if (method_exists($event, 'name')) {
            $event->name('publish-scheduled-posts');
        } else {
            $event->description('publish-scheduled-posts');
        }
    }
}
