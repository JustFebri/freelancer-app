<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class skill extends Model
{
    use HasFactory;
    protected $table = 'skill';
    protected $primaryKey = 'skill_id';
    public $timestamps = false;
    protected $fillable = [
        'skill_id',
        'skill_name',
    ];
}
