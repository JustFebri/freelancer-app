<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class saved_services extends Model
{
    use HasFactory;
    protected $table = 'saved_services';
    protected $primaryKey = 'saved_id';
    protected $fillable = [
        'saved_id', 'service_id', 'user_id',
    ];
}
