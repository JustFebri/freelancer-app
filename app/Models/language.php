<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class language extends Model
{
    use HasFactory;
    protected $table = 'language';
    protected $primaryKey = 'language_id';
    protected $timestamps = false;
}
