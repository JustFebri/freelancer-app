<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class sub_occupation extends Model
{
    use HasFactory;
    protected $table = 'sub_occupation';
    protected $primaryKey = 'subOccupation_id';
    public $timestamps = false;

    protected $fillable = [
        'subOccupation_id',
        'freelancer_id',
        'subcategory_id',
    ];
}
