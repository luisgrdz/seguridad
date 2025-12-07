<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CameraController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = Auth::user();
        $roleName = $user->role->name ?? 'user';
        $query = Camera::with('user');

        // 1. ADMIN: Ve todo
        if ($roleName === 'admin') {
            // Sin filtro
        }
        // 2. SUPERVISOR: Ve las suyas + equipo
        elseif ($roleName === 'supervisor') {
            $subordinateIds = $user->subordinates()->pluck('id');
            $query->where(function ($q) use ($user, $subordinateIds) {
                $q->where('user_id', $user->id)
                    ->orWhereIn('user_id', $subordinateIds);
            });
        }
        // 3. MANTENIMIENTO Y USUARIOS: Solo ven las suyas
        else {
            $query->where('user_id', $user->id);
        }

        $cameras = $query->orderBy('created_at', 'desc')->paginate(12);

        return view('cameras.index', compact('cameras'));
    }

    public function create()
    {
        // CAMBIO: Usamos $usersByRole para el Select agrupado
        $usersByRole = [];

        if (Auth::user()->role->name === 'admin') {
            // Traemos TODOS los usuarios y los agrupamos por rol
            $usersByRole = User::with('role')
                ->get()
                ->groupBy(function ($user) {
                    return $user->role->name;
                });
        }

        return view('cameras.create', compact('usersByRole'));
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

        // LÓGICA DE ASIGNACIÓN:
        if ($user->role->name === 'admin') {
            // Admin decide el dueño, o se la queda él
            $ownerId = $request->user_id ?? $user->id;
        } else {
            // MANTENIMIENTO: SE LA AUTO-ASIGNA SIEMPRE
            $ownerId = $user->id;
        }

        Camera::create([
            'name'     => $validated['name'],
            'ip'       => $validated['ip'],
            'location' => $validated['location'],
            'status'   => $validated['status'],
            'group'    => $validated['group'],
            'user_id'  => $ownerId,
        ]);

        return redirect()->route($this->getRoutePrefix() . 'cameras.index')
            ->with('success', 'Cámara registrada correctamente.');
    }

    public function show(Camera $camera)
    {
        $this->authorize('view', $camera);
        return view('cameras.show', compact('camera'));
    }

    public function edit(Camera $camera)
    {
        $this->authorize('update', $camera);

        $usersByRole = [];

        if (Auth::user()->role->name === 'admin') {
            $usersByRole = User::with('role')
                ->get()
                ->groupBy(function ($user) {
                    return $user->role->name;
                });
        }

        return view('cameras.edit', compact('camera', 'usersByRole'));
    }

    public function update(Request $request, Camera $camera)
    {
        $this->authorize('update', $camera);

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'ip'       => 'required|string',
            'location' => 'nullable|string|max:255',
            'status'   => 'required|boolean',
            'group'    => 'nullable|string|max:255',
            'user_id'  => 'nullable|exists:users,id'
        ]);

        // Solo el admin puede cambiar el dueño en la edición
        if (Auth::user()->role->name !== 'admin') {
            unset($validated['user_id']);
        }

        $camera->update($validated);

        return redirect()->route($this->getRoutePrefix() . 'cameras.index')
            ->with('success', 'Configuración actualizada.');
    }

    public function destroy(Camera $camera)
    {
        $this->authorize('delete', $camera);

        $camera->delete();

        return redirect()->route($this->getRoutePrefix() . 'cameras.index')
            ->with('success', 'Dispositivo eliminado.');
    }

    private function getRoutePrefix()
    {
        $role = Auth::user()->role->name ?? 'user';
        return match ($role) {
            'admin' => 'admin.',
            'supervisor' => 'supervisor.',
            'mantenimiento' => 'mantenimiento.',
            default => 'user.',
        };
    }
}
