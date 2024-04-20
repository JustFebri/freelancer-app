<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class personal_url extends Model
{
    use HasFactory;
    protected $table = 'personal_url';
    protected $primaryKey = 'url_id';
    public $timestamps = false;

    protected $fillable = [
        'freelancer_id',
        'personalUrl',
    ];
}
