<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    use HasFactory;
    protected $table = 'chat_rooms';
    protected $primaryKey = 'chatRoom_id';
    protected $guarded = ['chatRoom_id'];

    public function participants(): HasMany{
        return $this->hasMany(ChatParticipant::class, 'chatRoom_id');
    }

    public function messages(): HasMany{
        return $this->hasMany(ChatMessage::class, 'chatRoom_id');
    }

    public function lastMessage(): HasOne{
        return $this->hasOne(ChatMessage::class, 'chatRoom_id')->latest('updated_at');
    }

    public function scopeHasParticipant($query, int $userId){
        return $query->whereHas('participants',function($q) use ($userId){
            $q->where('user_id', $userId);
        });
    }
}
