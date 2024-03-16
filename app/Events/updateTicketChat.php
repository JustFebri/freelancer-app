<?php

namespace App\Events;

use App\Models\report_chats;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class updateTicketChat implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(private report_chats $reportChats)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // new PrivateChannel('chat.' . $this->chatMessage->chatRoom_id),
            new Channel('report.' . $this->reportChats->report_id),
        ];
    }

    public function broadcastAs(): String
    {
        return 'message.sent';
    }

    /**
     * Send back to client.
     *
     */

    public function broadcastWith(): array
    {
        return [
            'report_id' => $this->reportChats->chatRoom_id,
            'message' => $this->reportChats->message,
        ];
    }
}