<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;

class MessageSent extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private array $data)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via()
    {
        return [OneSignalChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toOneSignal()
    {
        $messageData = $this->data['messageData'];

        return OneSignalMessage::create()
            ->setSubject($messageData['senderName'] . " sent you a message.")
            ->setBody($messageData['message'])
            ->setData('data', $messageData);
    }
}
