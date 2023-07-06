<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class chat_room extends Model
{
    use HasFactory;
    protected $table = 'chat_room';
    protected $primaryKey = 'chatroom_id';
    protected $timestamps = false;
}
