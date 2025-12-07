<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = [
        'camera_id',
        'user_id',
        'type',
        'description',
        'priority',
        'status',
    ];

    // Relación: Una incidencia pertenece a una cámara
    public function camera()
    {
        return $this->belongsTo(Camera::class);
    }

    // Relación: Una incidencia es creada por un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
