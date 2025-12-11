<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Cargamos el usuario Y precargamos la relación 'role'
        // Esto evita la consulta "sorpresa" más adelante
        $user = Auth::user()->load('role');

        // 2. Comparamos el NOMBRE del rol, no el objeto entero
        // Usamos el operador null safe (?) por si el usuario no tiene rol asignado
        if ($user->role?->name === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('user.dashboard');
    }
}