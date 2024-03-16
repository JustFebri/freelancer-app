<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class report_chats extends Model
{
    use HasFactory;
    protected $table = 'report_chats';
    protected $primaryKey = 'reportChat_id';

    protected $fillable = [
        'reportChat_id',
        'user_id',
        'report_id',
        'admin_id',
        'message',

    ];
}
