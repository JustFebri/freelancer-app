<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class report extends Model
{
    use HasFactory;
    protected $table = 'report';
    protected $primaryKey = 'report_id';

    protected $fillable = [
        'report_id',
        'user_id',
        'order_id',
        'report_type',
        'description',
        'status',
        'subject',
        'admin_id',
    ];
}
