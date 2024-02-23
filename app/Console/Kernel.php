<?php

namespace App\Console;

use App\Models\custom_orders;
use App\Models\order;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            order::where('order_status', 'awaiting payment')
                ->where('due_date', '<', now())
                ->update(['order_status' => 'canceled']);

            // order::where('status', 'pending')
            //     ->get()
            //     ->each(function ($order) {

            //     });
            
            custom_orders::where('status', 'pending')
                ->where('expiration_date', '<', now())
                ->update(['status' => 'expired']);
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
