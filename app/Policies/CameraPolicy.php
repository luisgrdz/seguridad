<?php

namespace App\Policies;

use App\Models\Camera;
use App\Models\User;

class CameraPolicy
{
    /**
     * ¿Quién puede ver la lista de cámaras?
     * Todos pueden entrar, el filtro de qué ven se hace en la vista.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * ¿Quién puede ver una cámara específica?
     */
    public function view(User $user, Camera $camera): bool
    {
        $role = $user->role->name ?? '';

        // 1. ADMIN: Ve todo
        if ($role === 'admin') {
            return true;
        }

        // 2. SUPERVISOR: Ve las suyas y las de sus subordinados
        if ($role === 'supervisor') {
            $subordinateIds = $user->subordinates()->pluck('id')->toArray();
            return $camera->user_id === $user->id || in_array($camera->user_id, $subordinateIds);
        }

        // 3. MANTENIMIENTO Y USER: Solo ven si son los dueños (asignados por admin)
        return $user->id === $camera->user_id;
    }

    /**
     * ¿Quién puede CREAR?
     * AHORA: Admin Y Mantenimiento.
     */
    public function create(User $user): bool
    {
        $role = $user->role->name ?? '';
        return in_array($role, ['admin', 'mantenimiento']);
    }

    /**
     * ¿Quién puede EDITAR?
     * AHORA: Admin (todo) Y Mantenimiento (solo las suyas).
     * Supervisor y User: NO.
     */
    public function update(User $user, Camera $camera): bool
    {
        $role = $user->role->name ?? '';

        // Admin edita cualquiera
        if ($role === 'admin') {
            return true;
        }

        // Mantenimiento edita SOLO si es su cámara (asignada/creada por él)
        if ($role === 'mantenimiento') {
            return $user->id === $camera->user_id;
        }

        // Supervisor y User no pueden editar
        return false;
    }

    /**
     * ¿Quién puede ELIMINAR?
     * Por seguridad, dejaremos esto SOLO al Admin.
     * (Si quieres que mantenimiento borre, avísame).
     */
    public function delete(User $user, Camera $camera): bool
    {
        return ($user->role->name ?? '') === 'admin';
    }
}
