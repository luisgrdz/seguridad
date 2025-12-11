<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 1. Forzar HTTPS...
        if (App::isProduction()) {
            URL::forceScheme('https');
        }

        // 2. Prevenir Lazy Loading (CAMBIO AQUÍ)
        // Quitamos el "! App::isProduction()" y ponemos "true" directo.
        Model::preventLazyLoading(true);

        // Opcional: Impedir asignación masiva silenciosa
        Model::preventSilentlyDiscardingAttributes(true);
    }
}
