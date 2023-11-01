<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class freelancer_skill extends Model
{
    use HasFactory;
    protected $table = 'freelancer_skill';
    protected $primaryKey = 'freelancerskill_id';
    public $timestamps = false;

    protected $fillable = [
        'freelancerskill_id',
        'freelancer_id',
        'skill_id',
    ];
}
