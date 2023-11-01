<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class client extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'client';
    protected $primaryKey = 'client_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'orders_made',
        'total_spent',
    ];
}
