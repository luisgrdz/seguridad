<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; 
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Verificar Login
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();

        // 2. Cargar relación Role si falta
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        // 3. Validación de seguridad
        if (!$user->role) {
            Auth::logout();
            // A04 FIX: Mensaje genérico al usuario, detalle solo en logs internos si fuera necesario
            return abort(403, 'Error de autorización.');
        }

        // 4. Normalizar datos
        $userRoleName = strtolower(trim($user->role->name));

        foreach ($roles as $role) {
            if ($userRoleName === strtolower(trim($role))) {
                return $next($request);
            }
        }

        // --- A04 FIX: SOLUCIÓN DE SEGURIDAD ---

        // ANTES (Inseguro): Le decíamos al atacante qué roles existen.
        // return abort(403, "Acceso denegado. Tu rol actual es: '{$userRoleName}'. Esta sección requiere: " . implode(', ', $roles));

        // AHORA (Seguro):
        // 1. Guardamos el detalle en el log del sistema (storage/logs/laravel.log) para tus ojos solamente.
        Log::warning("Acceso denegado 403. Usuario ID: {$user->id} ({$userRoleName}) intentó acceder a ruta protegida para: " . implode(', ', $roles));

        // 2. Al usuario le mostramos un error estándar sin detalles sensibles.
        return abort(403);
    }
}
