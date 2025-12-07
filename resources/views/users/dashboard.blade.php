@extends('components.layouts.app')

@section('titulo', 'Inicio - Videovigilancia')

@section('contenido')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- HERO SECTION --}}
    <div class="text-center mb-20 fade-up relative">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[300px] bg-indigo-500/10 rounded-full blur-[100px] -z-10"></div>

        <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight mb-4">
            Sistema de <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">Videovigilancia</span>
        </h1>
        <p class="text-lg text-gray-500 max-w-2xl mx-auto">
            Plataforma centralizada para el monitoreo y gestión de seguridad en tiempo real.
        </p>

        <div class="mt-10 flex justify-center gap-4">
            @guest
                <a href="{{ route('login') }}" class="px-8 py-3 bg-indigo-600 text-white rounded-full font-medium shadow-lg shadow-indigo-500/30 hover:bg-indigo-700 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2">
                    Iniciar Sesión
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </a>
            @endguest

            @auth
                @php
                    $role = auth()->user()->role->name ?? 'user';
                    $route = match($role) {
                        'admin' => route('admin.cameras.index'),
                        'supervisor' => route('supervisor.cameras.index'),
                        'mantenimiento' => route('mantenimiento.cameras.index'),
                        default => route('user.cameras.index'),
                    };
                @endphp
                
                <a href="{{ $route }}" class="px-8 py-3 bg-white text-indigo-600 border border-indigo-100 rounded-full font-medium shadow-sm hover:shadow-md hover:border-indigo-200 transition-all duration-300 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Ver Cámaras
                </a>
            @endauth
        </div>
    </div>

    {{-- GRID DE ACCESOS / INFO (Solo visible si logueado) --}}
    @auth
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- TARJETA 1: Monitoreo --}}
        <a href="{{ $route }}" class="group block p-6 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-indigo-100 transition-all duration-300">
            <div class="h-12 w-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">Monitoreo Activo</h3>
            <p class="text-sm text-gray-500 mt-2">Accede al listado completo de dispositivos y visualiza transmisiones en vivo.</p>
        </a>

        {{-- TARJETA 2: Incidencias --}}
        @if(auth()->user()->role->name !== 'user')
            <a href="{{ route('incidents.index') }}" class="group block p-6 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-red-100 transition-all duration-300">
                <div class="h-12 w-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-red-600 transition-colors">Centro de Incidencias</h3>
                <p class="text-sm text-gray-500 mt-2">Revisa reportes de fallos, alertas de seguridad y estado de tickets.</p>
            </a>
        @else
            {{-- Para usuarios normales: Tarjeta informativa --}}
            <div class="p-6 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="h-12 w-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Estado del Sistema</h3>
                <p class="text-sm text-gray-500 mt-2">Todos los servicios operativos. Si detectas fallas, repórtalas desde el monitor.</p>
            </div>
        @endif

        {{-- TARJETA 3: Perfil / Admin --}}
        @if(auth()->user()->role->name === 'admin')
            <a href="{{ route('admin.personal.index') }}" class="group block p-6 bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-purple-100 transition-all duration-300">
                <div class="h-12 w-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">Gestión de Personal</h3>
                <p class="text-sm text-gray-500 mt-2">Administra usuarios, roles y permisos de acceso al sistema.</p>
            </a>
        @else
            <div class="p-6 bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="h-12 w-12 bg-gray-50 text-gray-600 rounded-xl flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Mi Perfil</h3>
                <p class="text-sm text-gray-500 mt-2">
                    Logueado como: <span class="font-medium text-indigo-600">{{ auth()->user()->name }}</span><br>
                    Rol: {{ ucfirst(auth()->user()->role->name) }}
                </p>
            </div>
        @endif

    </div>
    @endauth

    {{-- FOOTER SIMPLE --}}
    <div class="mt-20 text-center border-t border-gray-100 pt-8">
        <p class="text-sm text-gray-400">
            &copy; {{ date('Y') }} Sistema de Seguridad. Todos los derechos reservados.
        </p>
    </div>

</div>

@endsection