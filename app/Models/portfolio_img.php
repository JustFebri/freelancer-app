<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class portfolio_img extends Model
{
    use HasFactory;
    protected $table = 'portfolio_img';
    protected $primaryKey = 'portfolioImg_id';
    protected $timestamps = false;
}
