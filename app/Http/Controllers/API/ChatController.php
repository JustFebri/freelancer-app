<?php

namespace App\Http\Controllers\API;

use App\Events\NewMessageSent;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\GetMessageRequest;
use App\Http\Requests\API\GetProfileImages;
use App\Http\Requests\API\StoreChatRequest;
use App\Http\Requests\API\StoreMessageRequest;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Models\picture;
use App\Models\user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function createChat(StoreChatRequest $request)
    {
        Log::info('testing');
        $userId = auth()->id();
        $otherUserId = $request->otherUserId;

        if ($otherUserId == $userId) {
            return $this->error('You can not create a chat with your own');
        }

        $prevChat = $this->checkPrevChat($userId, $otherUserId);
        if ($prevChat == null) {
            $chat = Chat::create(['created_by' => $userId,]);
            $participants = [
                [
                    'chatRoom_id' => $chat->chatRoom_id,
                    'user_id' => $userId,
                ],
                [
                    'chatRoom_id' => $chat->chatRoom_id,
                    'user_id' => $otherUserId,
                ]
            ];
            foreach ($participants as $record) {
                ChatParticipant::create($record);
            }

            return response([
                'message' => 'Success',
                'chat' => $chat,
                'participants' => $participants
            ], 201);
        } else {
            return response([
                'message' => 'Failed chat founded',
                'data' => json_encode($prevChat),
            ], 200);
        }
    }
    private function checkPrevChat(int $userId, int $otherUserId)
    {
        return  DB::table('participants as cp1')
            ->join('participants as cp2', function ($join) use ($userId, $otherUserId) {
                $join->on('cp1.chatRoom_id', '=', 'cp2.chatRoom_id')
                    ->where('cp1.user_id', '=', $userId)
                    ->where('cp2.user_id', '=', $otherUserId);
            })
            ->first();
    }
    public function getAllChat()
    {
        $chat = Chat::hasParticipant(auth()->id())
            ->whereHas('messages')
            ->with('lastMessage.user', 'participants.user')
            ->latest('updated_at')
            ->get();

        return response([
            'message' => 'Success',
            'chatList' => $chat,
        ], 200);
    }

    public function getAllMessage(GetMessageRequest $request)
    {
        $data = $request->validated();
        $chatId = $data['chatRoom_id'];

        $messages = ChatMessage::where('chatRoom_id', $chatId)
            ->with('user')
            ->latest('created_at')
            ->get();

        return $this->success($messages);
    }

    public function sendMessage(StoreMessageRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $messageData = ChatMessage::create($data);
        $messageData->load('user');
        $this->sendNotificationToOther($messageData);

        return response([
            'data' => 'message',
            'message' => 'Message has been sent successfully.'
        ], 200);
    }

    private function sendNotificationToOther(ChatMessage $messageData)
    {

        // TODO move this event broadcast to observer
        broadcast(new NewMessageSent($messageData))->toOthers();

        $user = auth()->user();
        $userId = $user->user_id;

        $chat = Chat::where('chatRoom_id', $messageData->chatRoom_id)
            ->with(['participants' => function ($query) use ($userId) {
                $query->where('user_id', '!=', $userId);
            }])
            ->first();
        Log::info($chat);
        if (count($chat->participants) > 0) {
            $otherUserId = $chat->participants[0]->user_id;

            $otherUser = user::where('user_id', $otherUserId)->first();
            $otherUser->sendNewMessageNotification([
                'messageData' => [
                    'senderName' => $user->username,
                    'message' => $messageData->message,
                    'chatId' => $messageData->chatRoom_id
                ]
            ]);
        }
    }

    public function getProfileImage(GetProfileImages $profileImage)
    {
        $data = $profileImage->validated();

        $pic = picture::where('picture_id', $data['pic'])->first();
        return response([
            'pic' => $pic->picasset,
        ], 200);
    }
}
