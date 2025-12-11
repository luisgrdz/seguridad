<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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

    Route::get('/prueba-diagnostico', function () {
    // 1. Forzar encendido de la protección
    \Illuminate\Database\Eloquent\Model::preventLazyLoading(true);

    // 2. Preguntar al sistema si REALMENTE está encendida
    $estaProtegido = \Illuminate\Database\Eloquent\Model::preventsLazyLoading();

    // 3. Buscar la cámara
    $camera = \App\Models\Camera::whereNotNull('user_id')->first();

    if (!$camera) return "ERROR: No hay cámaras con usuario asignado.";

    // 4. Verificar si la relación 'user' ya vino cargada "mágicamente" (Eager Loading)
    $yaEstabaCargada = $camera->relationLoaded('user');

    echo "<h1>Diagnóstico de Seguridad</h1>";
    echo "<ul>";
    echo "<li><strong>¿Protección activada?:</strong> " . ($estaProtegido ? '✅ SÍ' : '❌ NO') . "</li>";
    echo "<li><strong>¿Relación 'user' precargada?:</strong> " . ($yaEstabaCargada ? '⚠️ SÍ (Esto evitaría el error)' : '✅ NO (Correcto)') . "</li>";
    echo "</ul>";

    echo "<h3>Intentando acceder a la relación prohibida...</h3>";

    try {
        // AQUÍ ES DONDE DEBERÍA EXPLOTAR
        $nombre = $camera->user->name;

        // Si llega aquí, falló la seguridad
        echo "<h2 style='color:red'>❌ FALLÓ: Se permitió el acceso. Nombre: {$nombre}</h2>";
        echo "<p>Laravel ignoró la restricción. Posible causa: Versión del framework o configuración global sobrescrita.</p>";
    } catch (\Illuminate\Database\LazyLoadingViolationException $e) {
        // Si entra aquí, ¡FUNCIONÓ!
        echo "<h2 style='color:green'>✅ ÉXITO: Se bloqueó el acceso.</h2>";
        echo "<p>Mensaje capturado: <em>" . $e->getMessage() . "</em></p>";
    } catch (\Exception $e) {
        echo "<h2>⚠️ Error inesperado: " . $e->getMessage() . "</h2>";
    }});
