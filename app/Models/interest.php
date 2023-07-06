<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class interest extends Model
{
    use HasFactory;
    protected $table = 'interest';
    protected $primaryKey = 'interest_id';
    protected $timestamps = false;
}
