<?php

namespace App\Policies;

use App\Models\Camera;
use App\Models\User;

class CameraPolicy
{
    /**
     * Determina si el usuario puede ver la lista de cámaras.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede ver una cámara específica.
     */
    public function view(User $user, Camera $camera): bool
    {
        $role = $user->role->name ?? '';

        // SOLO Admin ve todo sin restricciones
        if ($role === 'admin') {
            return true;
        }

        // Supervisor: ve las suyas y las de sus subordinados
        if ($role === 'supervisor') {
            $subordinateIds = $user->subordinates()->pluck('id')->toArray();
            return $camera->user_id === $user->id || in_array($camera->user_id, $subordinateIds);
        }

        // Usuario Normal y Mantenimiento:
        // Solo pueden verla si se les ha asignado explícitamente (son el user_id de la cámara)
        return $user->id === $camera->user_id;
    }

    /**
     * Determina si el usuario puede crear cámaras.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determina si el usuario puede actualizar la cámara.
     */
    public function update(User $user, Camera $camera): bool
    {
        $role = $user->role->name ?? '';

        // SOLO Admin puede editar cualquier cámara
        if ($role === 'admin') {
            return true;
        }

        // Supervisor: Lógica personalizada si permites que editen (opcional)
        // Por ahora, asumimos que solo pueden editar si son los dueños directos
        // o si permites que editen las de sus subordinados.
        if ($role === 'supervisor') {
            // Ejemplo: Solo edita las suyas, no las de subordinados
            return $user->id === $camera->user_id;
        }

        // Usuario Normal y Mantenimiento:
        // Solo pueden editar si son los dueños (se les asignó la cámara)
        return $user->id === $camera->user_id;
    }

    /**
     * Determina si el usuario puede eliminar la cámara.
     */
    public function delete(User $user, Camera $camera): bool
    {
        // Solo el admin puede eliminar
        return $user->role->name === 'admin';
    }
}
