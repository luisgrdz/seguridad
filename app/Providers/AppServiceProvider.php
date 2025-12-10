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
        // 1. Forzar HTTPS en producción (OWASP A05)
        if (App::isProduction()) {
            URL::forceScheme('https');
        }

        // 2. Prevenir Lazy Loading (OWASP A05)
        // Esto protege contra problemas de rendimiento y ataques de DoS por consultas masivas.
        // Aunque tu versión beta actual no lo detecte, este es el código correcto.
        Model::preventLazyLoading(! App::isProduction());

        // Opcional: Impedir asignación masiva silenciosa (Lanza error si intentas guardar un campo no permitido)
        Model::preventSilentlyDiscardingAttributes(! App::isProduction());
    }
}
