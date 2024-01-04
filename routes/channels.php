<?php

use App\Models\ChatParticipant;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('chat.{id}', function ($user, $id) {
//     Log::info("attempting to autorize user id");
//     $participant = ChatParticipant::where([
//         [
//             'user_id', $user->id,
//         ],
//         [
//             'chatRoom_id', $id,
//         ]
//     ])->first();

//     Log::info('User ID: ' . $user->id);
//     Log::info('Chat Room ID: ' . $id);
//     Log::info('Participant: ' . json_encode($participant));

//     return $participant !== null;
// });

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

