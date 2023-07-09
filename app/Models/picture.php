<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class picture extends Model
{
    use HasFactory;
    protected $table = 'picture';
    protected $primaryKey = 'picture_id';
    protected $fillable = ['file', 'filetype', 'filename', 'updated_at'];
}
