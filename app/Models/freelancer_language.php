<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class freelancer_language extends Model
{
    use HasFactory;
    protected $table = 'freelancer_language';
    protected $primaryKey = 'freelancerlanguage_id';
    public $timestamps = false;

    protected $fillable = [
        'freelancerlanguage_id',
        'freelancer_id',
        'language_id',
        'proficiency_level'
    ];
}
