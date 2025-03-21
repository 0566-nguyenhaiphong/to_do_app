<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // ...existing code...

    protected function schedule(Schedule $schedule): void
    {
        // ...existing code...

        // Schedule the task to check for due or overdue tasks
        $schedule->call(function () {
            \App\Models\Task::checkDueTasks();
        })->hourly();
    }

    // ...existing code...
}
