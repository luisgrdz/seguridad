<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Videovigilancia')</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100">

    <nav class="bg-white shadow px-6 py-3 flex justify-between items-center">
        <div class="font-bold text-xl flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
            Videovigilancia
        </div>

        @auth
        <div class="flex items-center gap-6">

            {{-- 1. ENLACE DE NOTIFICACIONES (Solo Admin/Supervisor/Mant) --}}
            @if(auth()->user()->role->name !== 'user') 
                <a href="{{ route('incidents.index') }}" class="relative text-gray-600 hover:text-indigo-600 transition font-medium flex items-center gap-1">
                    {{-- Icono Campana --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    Incidencias

                    {{-- Badge Rojo de Conteo (Consulta directa simple para la vista) --}}
                    @php
                        $pendingCount = \App\Models\Incident::where('status', 'pendiente')->count();
                    @endphp
                    
                    @if($pendingCount > 0)
                        <span class="absolute -top-2 -right-3 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm animate-pulse">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
            @endif

            {{-- 2. ENLACES DASHBOARD --}}
            @if(auth()->user()->role->name === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-blue-600 font-medium">Panel Admin</a>
            @else
                <a href="{{ route('user.dashboard') }}" class="text-gray-600 hover:text-blue-600 font-medium">Mi Panel</a>
            @endif

            {{-- 3. CERRAR SESIÓN --}}
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button class="text-red-500 hover:text-red-700 font-medium border border-red-200 px-3 py-1 rounded hover:bg-red-50 transition">
                    Salir
                </button>
            </form>
        </div>
        @endauth
    </nav>

    <div class="container mx-auto mt-6 px-4">
        {{-- Mensajes Flash de éxito (Opcional, muy útil para el reporte) --}}
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">¡Éxito!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @yield('contenido')
    </div>

</body>
</html>