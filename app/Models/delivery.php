<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class delivery extends Model
{
    use HasFactory;
    protected $table = 'delivery';
    protected $primaryKey = 'delivery_id';
    protected $fillable = [
        'order_id',
        'fileUrl',
        'description',
        'created_at',
        'updated_at',
    ];
}
