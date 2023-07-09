<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class client extends Model
{
    use HasFactory;
    protected $table = 'client';
    protected $primaryKey = 'client_id';

    protected $fillable = ['name', 'email', 'location', 'picture_id', 'status', 'updated_at'];
}
