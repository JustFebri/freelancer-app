<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class picture extends Model
{
    use HasFactory;
    protected $table = 'picture';
    protected $primaryKey = 'picture_id';
    protected $fillable = ['piclink', 'picasset', 'created_at', 'updated_at',];
}
