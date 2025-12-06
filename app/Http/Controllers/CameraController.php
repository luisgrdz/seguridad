<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CameraController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $roleName = $user->role->name ?? 'user';
        $query = Camera::with('user');

        // 1. SOLO ADMIN: Ve TODAS las cámaras
        if ($roleName === 'admin') {
            // No aplicamos filtro, ve todo
        }
        // 2. SUPERVISOR: Ve las suyas + las de sus subordinados
        elseif ($roleName === 'supervisor') {
            $subordinateIds = $user->subordinates()->pluck('id');
            $query->where(function ($q) use ($user, $subordinateIds) {
                $q->where('user_id', $user->id)
                    ->orWhereIn('user_id', $subordinateIds);
            });
        }
        // 3. MANTENIMIENTO Y USUARIO: Solo ven las que el admin les haya asignado (su user_id)
        else {
            $query->where('user_id', $user->id);
        }

        $cameras = $query->paginate(12);
        return view('cameras.index', compact('cameras'));
    }

    public function create()
    {
        $userRole = Auth::user()->role->name;

        // SOLO Admin puede ver la lista para asignar dueño manualmente al inicio
        $users = ($userRole === 'admin') ? \App\Models\User::all() : [];

        return view('cameras.create', compact('users'));
    }

    public function edit(Camera $camera)
    {
        $userRole = Auth::user()->role->name;

        // SOLO Admin puede reasignar dueño
        $users = ($userRole === 'admin') ? \App\Models\User::all() : [];

        return view('cameras.edit', compact('camera', 'users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'ip'       => 'required|string',
            'location' => 'nullable|string|max:255',
            'status'   => 'required|boolean',
            'group'    => 'nullable|string|max:255',
            'user_id'  => 'nullable|exists:users,id'
        ]);

        $user = Auth::user();
        $roleName = $user->role->name;

        // Lógica de asignación de dueño
        if ($roleName === 'admin') {
            // Admin puede elegir a cualquiera, o asignársela a sí mismo si no eligió nada
            $ownerId = !empty($request->user_id) ? $request->user_id : $user->id;
        } elseif ($roleName === 'mantenimiento') {
            // MANTENIMIENTO: Se asigna AUTOMÁTICAMENTE al Admin principal
            // Buscamos al primer usuario con rol 'admin'
            $adminUser = \App\Models\User::whereHas('role', function ($q) {
                $q->where('name', 'admin');
            })->first();

            // Si por alguna razón no hay admin, fallback al usuario actual (pero esto no debería pasar)
            $ownerId = $adminUser ? $adminUser->id : $user->id;
        } else {
            // Otros roles (si tuvieran permiso de crear): Se asignan a sí mismos
            $ownerId = $user->id;
        }

        Camera::create([
            ...$validated,
            'user_id' => $ownerId,
        ]);

        // Redirección
        $prefix = match ($roleName) {
            'admin' => 'admin.',
            'supervisor' => 'supervisor.',
            'mantenimiento' => 'mantenimiento.',
            default => 'user.',
        };

        return redirect()->route($prefix . 'cameras.index')
            ->with('success', 'Cámara registrada correctamente.');
    }

    public function show(Camera $camera)
    {
        return view('cameras.show', compact('camera'));
    }

    

    public function update(Request $request, Camera $camera)
    {
        $user = Auth::user();
        $userRole = $user->role->name;

        // Permisos para editar
        if ($userRole !== 'admin' && $userRole !== 'mantenimiento' && $camera->user_id !== $user->id) {
            abort(403, 'No autorizado');
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'ip'       => 'required|string',
            'location' => 'nullable|string|max:255',
            'status'   => 'required|boolean',
            'group'    => 'nullable|string|max:255',
            'user_id'  => 'nullable|exists:users,id'
        ]);

        // Solo Admin y Mantenimiento pueden cambiar el dueño
        if ($userRole !== 'admin' && $userRole !== 'mantenimiento') {
            unset($validated['user_id']);
        }

        $camera->update($validated);

        $prefix = match ($userRole) {
            'admin' => 'admin.',
            'supervisor' => 'supervisor.',
            'mantenimiento' => 'mantenimiento.',
            default => 'user.',
        };

        return redirect()->route($prefix . 'cameras.index')->with('success', 'Cámara actualizada.');
    }

    public function destroy(Camera $camera)
    {
        $camera->delete();
        return redirect()->back()->with('success', 'Eliminada');
    }
}
