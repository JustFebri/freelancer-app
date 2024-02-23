<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class custom_orders extends Model
{
    use HasFactory;
    protected $table = 'custom_orders';
    protected $primaryKey = 'custom_id';
    protected $fillable = [
        'service_id',
        'freelancer_id',
        'description',
        'price',
        'revision',
        'delivery_days',
        'status',
        'expiration_date',
    ];
}
