<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class service extends Model
{
    use HasFactory;
    protected $table = 'service';
    protected $primaryKey = 'service_id';

    protected $fillable = [
        'freelancer_id',
        'subcategory_id',
        'title',
        'description',
        'location',
        'type',
        'status',
        'custom_order',
        'IsApproved',
    ];

    public function user(){
        return $this->belongsTo(user::class);
    }
}
