<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class freelancer extends Model
{
    use HasFactory;
    protected $table = 'freelancer';
    protected $primaryKey = 'freelancer_id';
}
