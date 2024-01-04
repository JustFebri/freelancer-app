<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;
    protected $table = 'messages';
    protected $guarded = ['message_id'];
    protected $primaryKey = 'message_id';

    protected $touches = ['chat'];

    public function user():BelongsTo
    {
        return $this->belongsTo(user::class, 'user_id');
    }

    public function chat():BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chatRoom_id');
    }
}
