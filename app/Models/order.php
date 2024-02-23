<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    use HasFactory;
    protected $table = 'order';
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'order_id',
        'package_id',
        'client_id',
        'order_status',
        'custom_id',
        'due_date',
        'freelancer_id',
        'amount',
    ];
}
