<?php

namespace App\Console;

use App\Events\UpdateCustom;
use App\Events\UpdateOrder;
use App\Events\UpdateOrderFreelancer;
use App\Models\ChatMessage;
use App\Models\custom_orders;
use App\Models\freelancer;
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
            $awaitingOrders = order::where('order_status', 'awaiting payment')
                ->where('due_date', '<', now())
                ->get();

            foreach ($awaitingOrders as $order) {
                $order->update(['order_status' => 'canceled']);
                broadcast(new UpdateOrder($order->client_id))->toOthers();
            }

            order::where('order_status', 'token')
                ->where('due_date', '<', now())
                ->delete();

            $deliveredOrders = order::where('order_status', 'delivered')
                ->where('due_date', '<', now())
                ->get();

            foreach ($deliveredOrders as $order) {
                $order->update(['order_status' => 'completed']);

                broadcast(new UpdateOrder($order->client_id))->toOthers();

                $freelancer = freelancer::find($order->freelancer_id);
                broadcast(new UpdateOrderFreelancer($freelancer->user_id))->toOthers();
            }


            $pendingOrders = order::where('order_status', 'pending')
                ->where('due_date', '<', now())
                ->get();

            foreach ($pendingOrders as $order) {
                app('App\Http\Controllers\API\MidtransController')->refund($order->order_id);
                broadcast(new UpdateOrder($order->client_id))->toOthers();

                $freelancer = freelancer::find($order->freelancer_id);
                broadcast(new UpdateOrderFreelancer($freelancer->user_id))->toOthers();
            }

            $custom_orders = custom_orders::where('status', 'pending')
                ->where('expiration_date', '<', now())
                ->get();

            foreach ($custom_orders as $custom_order) {
                $custom_order->update(['status' => 'expired']);
                $chatRoom = ChatMessage::where('custom_id',$custom_order->custom_id);
                broadcast(new UpdateCustom($chatRoom->chatRoom_id))->toOthers();
            }
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
