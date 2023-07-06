<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class verification_request extends Model
{
    use HasFactory;
    protected $table = 'verification_request';
    protected $primaryKey = 'request_id';
}
