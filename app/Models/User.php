<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str; // <--- Importante para manejar cadenas

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'supervisor_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELACIONES ---

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function cameras()
    {
        return $this->hasMany(Camera::class);
    }

    // Relación: Usuarios que están a cargo de este usuario (Subordinados)
    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    // Relación: El jefe de este usuario (Supervisor)
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    // *** SOLUCIÓN DEL ERROR ***
    // Alias para 'subordinates', ya que el controlador lo llama 'supervisedUsers'
    public function supervisedUsers()
    {
        return $this->subordinates();
    }
    // --- MÉTODOS AUXILIARES ---

    /**
     * Generar iniciales del usuario (Ej: "Juan Pérez" -> "JP")
     * Soluciona el error BadMethodCallException
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->take(2)
            ->implode('');
    }
    /**
     * ACCESOR: Traduce el rol técnico a un nombre profesional.
     * Uso en vista: {{ $user->role_label }}
     */
    public function getRoleLabelAttribute()
    {
        // Si por alguna razón no tiene rol, devuelve 'Usuario'
        $role = $this->role->name ?? '';

        return match ($role) {
            'admin'         => 'Administrador General',
            'supervisor'    => 'Supervisor de Zona',
            'mantenimiento' => 'Técnico de Soporte',
            'user'          => 'Guardia de Seguridad', // <--- Aquí el cambio clave
            default         => ucfirst($role),
        };
    }

}
