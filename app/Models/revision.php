<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class revision extends Model
{
    use HasFactory;
    protected $table = 'revision';
    protected $primaryKey = 'revision_id';
    protected $fillable = [
        'order_id',
        'notes',
        'status',
        'response',
        'created_at',
        'updated_at',
    ];
}
