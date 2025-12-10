<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class PersonalController extends Controller
{
    public function index()
    {
        // Cargamos 'role' y 'supervisor' para optimizar la consulta y mostrar el encargado
        $users = User::with(['role', 'supervisor'])->where('role_id', '!=', 1)->paginate(10);
        return view('personal.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('name', '!=', 'admin')->get();

        $supervisors = User::whereHas('role', function ($q) {
            $q->where('name', 'supervisor');
        })->get();

        return view('personal.create', compact('roles', 'supervisors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id'  => 'required|exists:roles,id',
            'supervisor_id' => 'nullable|exists:users,id'
        ]);

        // --- CAMBIO DE SEGURIDAD ---
        // Instanciamos el modelo manualmente
        $user = new User();

        // Asignamos campos seguros (que podrían estar en fillable)
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->status = 1; // Por defecto activo

        // Asignamos explícitamente los campos protegidos (NO están en fillable)
        // Esto asegura que solo el Admin (quien usa este controlador) pueda tocarlos.
        $user->role_id = $request->role_id;
        $user->supervisor_id = $request->supervisor_id;

        // Guardamos en BD
        $user->save();

        return redirect()->route('admin.personal.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user)
    {
        $roles = Role::where('name', '!=', 'admin')->get();

        $supervisors = User::whereHas('role', function ($q) {
            $q->where('name', 'supervisor');
        })
            ->where('id', '!=', $user->id)
            ->get();

        return view('personal.edit', compact('user', 'roles', 'supervisors'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string',
            'email' => "required|email|unique:users,email,$user->id",
            'role_id' => 'required|exists:roles,id',
            'supervisor_id' => 'nullable|exists:users,id'
        ]);

        // Asignación manual de campos
        $user->name = $request->name;
        $user->email = $request->email;

        // Solo actualizamos password si viene en el request
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // --- CAMBIO DE SEGURIDAD ---
        // Asignación explícita de roles y supervisor
        $user->role_id = $request->role_id;
        $user->supervisor_id = $request->supervisor_id;

        // Guardamos los cambios
        $user->save();

        return redirect()->route('admin.personal.index')
            ->with('success', 'Usuario actualizado.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.personal.index')
            ->with('success', 'Usuario eliminado.');
    }

    public function toggle(User $user)
    {
        $user->update([
            'status' => !$user->status
        ]);

        return redirect()->route('admin.personal.index')
            ->with('success', 'Estado del usuario actualizado.');
    }
}
