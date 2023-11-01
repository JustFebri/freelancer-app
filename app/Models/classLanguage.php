<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LanguageClass extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = ['language', 'level'];
}
