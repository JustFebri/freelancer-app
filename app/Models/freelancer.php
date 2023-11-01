<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class freelancer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'freelancer';
    protected $primaryKey = 'freelancer_id';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'identity_number',
        'identity_name',
        'identity_gender',
        'identity_address',
        'description',
        'rating',
        'total_sales',
        'revenue',
        'link',
        'id_card',
        'id_card_with_selfie',
        'isApproved'
    ];
}
