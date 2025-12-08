<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    
use Auditable;

    protected $fillable = [
        'name',
        'ip',
        'location',
        'status',
        'group',
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}