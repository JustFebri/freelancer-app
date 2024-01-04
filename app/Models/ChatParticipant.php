<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatParticipant extends Model
{
    use HasFactory;
    protected $table = 'participants';
    protected $primaryKey = 'participant_id';
    protected $guarded = ['participant_id'];
    public function user():BelongsTo
    {
        return $this->belongsTo(user::class, 'user_id');
    }
}
