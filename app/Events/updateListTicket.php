<?php

namespace App\Events;

use App\Models\report;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class updateListTicket implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(private string $user_id)
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
            new Channel('update.' . $this->user_id),
        ];
    }

    public function broadcastAs(): String
    {
        return 'message.update';
    }

    /**
     * Send back to client.
     *
     */

    public function broadcastWith(): array
    {
        return [
            'message' => 'Update Last Ticket Message',
        ];
    }
}
