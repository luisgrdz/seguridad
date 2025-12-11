<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configuración de Strict Mode (usando la Facade App importada arriba)
        Model::shouldBeStrict(! App::isProduction());

        // Detector de la consulta SQL sospechosa
        DB::listen(function ($query) {
            // Buscamos si la consulta contiene "select * from roles"
            if (str_contains($query->sql, 'select * from roles')) {

                // Registramos la alerta en el log
                Log::critical('ALERTA: Consulta a ROLES detectada en: ' . request()->url());

                // Opcional: Descomenta la siguiente línea para detener la app y ver el error en pantalla
                // dd('Consulta detectada:', $query->sql);
            }
        });
    }
}
