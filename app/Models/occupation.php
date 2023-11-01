<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class occupation extends Model
{
    use HasFactory;
    protected $table = 'occupation';
    protected $primaryKey = 'occupation_id';
    public $timestamps = false;

    protected $fillable = [
        'occupation_id',
        'freelancer_id',
        'category_id',
        'from',
        'to',
    ];
}
