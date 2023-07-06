<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class service_package extends Model
{
    use HasFactory;
    protected $table = 'service_package';
    protected $primaryKey = 'package_id';
}
