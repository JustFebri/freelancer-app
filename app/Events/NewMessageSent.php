<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(private ChatMessage $chatMessage)
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
            new Channel('chat.' . $this->chatMessage->chatRoom_id),
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
            'chatRomm_id' => $this->chatMessage->chatRomm_id,
            'message' => $this->chatMessage->toArray(),
        ];
    }
}
