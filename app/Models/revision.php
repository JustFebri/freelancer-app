<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class revision extends Model
{
    use HasFactory;
    protected $table = 'revision';
    protected $primaryKey = 'rev_id';
}
