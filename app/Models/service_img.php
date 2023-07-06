<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class service_img extends Model
{
    use HasFactory;
    protected $table = 'service_img';
    protected $primaryKey = 'serviceImg_id';
    protected $timestamps = false;
}
