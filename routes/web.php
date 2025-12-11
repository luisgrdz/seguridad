<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\app;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Config;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IncidentController; 
use App\Models\Camera;

Route::view('/', 'index')->name('index');

// Auth
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1') // Máximo 5 intentos por minuto
    ->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// --- RUTAS GENERALES / COMPARTIDAS (Cualquier usuario logueado) ---
// Aquí van las incidencias para que todos los roles puedan reportar
Route::middleware(['auth', 'no_cache'])->group(function () {

    // Formulario de reporte (GET)
    Route::get('/cameras/{camera}/report', [IncidentController::class, 'create'])
        ->name('incidents.create');

    // Guardar el reporte (POST)
    Route::post('/incidents', [IncidentController::class, 'store'])
        ->name('incidents.store');

    // RUTAS DE PERFIL
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});


// --- RUTAS DE ADMINISTRADOR ---
Route::middleware(['auth', 'no_cache', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::prefix('personal')->name('personal.')->group(function () {
            Route::get('/', [PersonalController::class, 'index'])->name('index');
            Route::get('/create', [PersonalController::class, 'create'])->name('create');
            Route::post('/', [PersonalController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [PersonalController::class, 'edit'])->name('edit');
            Route::patch('/{user}', [PersonalController::class, 'update'])->name('update');
            Route::delete('/{user}', [PersonalController::class, 'destroy'])->name('destroy');
            Route::patch('/{user}/toggle', [PersonalController::class, 'toggle'])->name('toggle');
        });

        Route::resource('cameras', CameraController::class);
    });

// --- RUTAS DE USUARIO NORMAL ---
Route::middleware(['auth', 'no_cache', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

        Route::prefix('cameras')->name('cameras.')->group(function () {
            Route::get('/', [CameraController::class, 'index'])->name('index');
            // Aunque la policy bloquee create/store, las rutas existen para mantener estructura,
            // pero el controlador y la policy harán el trabajo sucio de seguridad.
            Route::get('/create', [CameraController::class, 'create'])->name('create');
            Route::get('/{camera}/edit', [CameraController::class, 'edit'])->name('edit');
            Route::post('/', [CameraController::class, 'store'])->name('store');
            Route::get('/{camera}', [CameraController::class, 'show'])->name('show');
        });
    });

// --- RUTAS DE SUPERVISOR ---
Route::middleware(['auth', 'no_cache', 'role:supervisor'])
    ->prefix('supervisor')
    ->name('supervisor.')
    ->group(function () {
        Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');

        Route::prefix('cameras')->name('cameras.')->group(function () {
            Route::get('/', [CameraController::class, 'index'])->name('index');
            Route::get('/create', [CameraController::class, 'create'])->name('create');
            Route::get('/{camera}/edit', [CameraController::class, 'edit'])->name('edit');
            Route::post('/', [CameraController::class, 'store'])->name('store');
            Route::get('/{camera}', [CameraController::class, 'show'])->name('show');
        });
    });

// --- RUTAS DE MANTENIMIENTO ---
Route::middleware(['auth', 'no_cache', 'role:mantenimiento'])
    ->prefix('mantenimiento')
    ->name('mantenimiento.')
    ->group(function () {

        // Dashboard simple
        Route::get('/dashboard', function () {
            $totalCameras = Camera::count();
            $offlineCameras = Camera::where('status', false)->count();
            return view('mantenimiento.dashboard', compact('totalCameras', 'offlineCameras'));
        })->name('dashboard');

        // Reutilizamos el CameraController (La Policy filtra qué pueden ver/editar)
        Route::resource('cameras', CameraController::class);
    });

Route::resource('incidents', IncidentController::class);

// routes/web.php

Route::get('/estado-seguridad', function () {
    return [
        'Tu Entorno Actual (App::environment)' => App::environment(),
        '¿Es Producción? (App::isProduction)' => App::isProduction() ? 'SÍ' : 'NO',
        '¿Bloqueo Lazy Loading Activo?' => Model::preventsLazyLoading() ? 'ACTIVO (Seguro)' : 'INACTIVO (Peligro)',
    ];
});

Route::get('/prueba-lazy-final', function () {
    // 1. Forzamos la configuración para estar 100% seguros
    \Illuminate\Database\Eloquent\Model::preventLazyLoading(true);

    echo "<h1>Prueba Final de Seguridad</h1>";

    try {
        $user = \App\Models\User::first();

        if (!$user) return "Error: Crea un usuario primero.";

        echo "Usuario: {$user->name}<br>";
        echo "Intentando leer su ROL (Lazy Load)...<br>";

        // CORRECCIÓN AQUÍ: Usamos 'role' (singular) que sí existe en tu User.php
        $nombreRol = $user->role ? $user->role->name : 'Sin rol';

        // Si el código llega aquí, la seguridad falló
        return '<h2 style="color:red">❌ FALLO: Se permitió el acceso (Lazy Loading no bloqueado).</h2>';
    } catch (\Illuminate\Database\LazyLoadingViolationException $e) {
        // Si entra aquí, la seguridad funcionó
        return '<div style="border: 2px solid green; padding: 20px; background: #e6fffa; color: #047857;">
                    <h1>✅ ¡BLOQUEO EXITOSO!</h1>
                    <p>El sistema detuvo la consulta antes de que ocurriera.</p>
                    <small>Laravel dijo: ' . $e->getMessage() . '</small>
                </div>';
    }
});

// PRUEBA 1: Lazy Loading (N+1)
Route::get('/test-lazy', function () {
    echo "<h2>Prueba de Lazy Loading (Carga Perezosa)</h2>";
    try {
        $user = User::first();
        if (!$user) return "⚠️ Crea un usuario primero para probar.";

        // Intentamos acceder a la relación 'role' sin haber usado with('role')
        // Si la seguridad funciona, esto debe lanzar una excepción.
        $role = $user->role;

        return '<div style="color:red; border:2px solid red; padding:10px;">
                    ❌ FALLO: Se permitió el Lazy Loading. <br>
                    La relación se cargó sin bloquearse.
                </div>';
    } catch (\Illuminate\Database\LazyLoadingViolationException $e) {
        return '<div style="color:green; border:2px solid green; padding:10px; background:#e6fffa;">
                    ✅ <strong>ÉXITO: Lazy Loading Bloqueado.</strong><br>
                    Laravel detuvo la consulta correctamente.<br>
                    <small>Error capturado: ' . $e->getMessage() . '</small>
                </div>';
    }
});

// PRUEBA 2: Asignación Masiva (Mass Assignment)
Route::get('/test-mass', function () {
    echo "<h2>Prueba de Asignación Masiva</h2>";
    try {
        // Intentamos crear un usuario inyectando 'role_id' o 'is_admin'
        // que NO están en tu array $fillable.
        // Si preventSilentlyDiscardingAttributes(true) funciona, esto debe fallar.
        $user = new User([
            'name' => 'Hacker Test',
            'email' => 'hacker@test.com',
            'password' => '12345678',
            'role_id' => 999, // <--- Este campo está protegido (guarded) en tu User.php
            'campo_inexistente' => 'valor peligroso' // <--- Campo basura
        ]);

        return '<div style="color:red; border:2px solid red; padding:10px;">
                    ❌ FALLO: Se permitió la Asignación Masiva.<br>
                    Laravel ignoró silenciosamente los campos prohibidos sin avisar.
                </div>';
    } catch (\Illuminate\Database\Eloquent\MassAssignmentException $e) {
        return '<div style="color:green; border:2px solid green; padding:10px; background:#e6fffa;">
                    ✅ <strong>ÉXITO: Asignación Masiva Bloqueada.</strong><br>
                    Laravel detectó el intento de inyectar campos no permitidos.<br>
                    <small>Error capturado: ' . $e->getMessage() . '</small>
                </div>';
    }
});

Route::get('/debug-lazy', function () {
    // 1. Limpiamos cualquier estado previo
    Model::preventLazyLoading(false); // Reseteamos

    // 2. Aplicamos la protección MANUALMENTE aquí para probar aislamiento
    Model::preventLazyLoading(true);

    $user = User::first();

    if (!$user) {
        return ['error' => 'No hay usuarios en la BD. Crea uno para probar.'];
    }

    return [
        '1. Estado del Modelo' => [
            '¿La protección está activada globalmente?' => Model::preventsLazyLoading() ? 'SÍ (Correcto)' : 'NO (Error grave)',
            'Clase del Usuario' => get_class($user),
            '¿La relación "role" ya está cargada?' => $user->relationLoaded('role') ? 'SÍ (No se consultará)' : 'NO (Debería consultar)',
            'ID del Rol (Foreign Key)' => $user->role_id,
        ],
        '2. Prueba de Fuego' => [
            'Intento de acceso' => 'Leyendo $user->role...',
            'Resultado' => (function () use ($user) {
                try {
                    // INTENTO DE VIOLACIÓN
                    $role = $user->role;
                    return "❌ FALLO: Se leyó el rol '{$role?->name}' sin error.";
                } catch (\Illuminate\Database\LazyLoadingViolationException $e) {
                    return "✅ ÉXITO: Se capturó la excepción LazyLoadingViolationException.";
                } catch (\Exception $e) {
                    return "⚠️ OTRO ERROR: " . get_class($e) . " - " . $e->getMessage();
                }
            })(),
        ],
        '3. Verificación de AppServiceProvider' => [
            'Entorno' => app()->environment(),
            'Debug' => config('app.debug'),
        ]
    ];
});