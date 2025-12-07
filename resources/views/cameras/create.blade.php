@extends('components.layouts.app')

@section('titulo', 'Registrar cámara')

@section('contenido')

@php
    $userRole = Auth::user()->role->name ?? 'user';
    
    // Prefijo para la ruta del formulario
    $prefix = match($userRole) {
        'admin' => 'admin.',
        'supervisor' => 'supervisor.',
        'mantenimiento' => 'mantenimiento.',
        default => 'user.',
    };

    // Configuración visual para el selector de Admin
    $roleLabels = [
        'mantenimiento' => '🛠️ Personal de Mantenimiento',
        'supervisor'    => '👀 Supervisores',
        'user'          => '👤 Usuarios / Guardias',
        'admin'         => '🛡️ Administradores'
    ];
    
    // Orden de aparición en el select
    $priorityOrder = ['mantenimiento', 'supervisor', 'user', 'admin'];
@endphp

<div class="max-w-2xl mx-auto">
    
    {{-- Botón Volver --}}
    <div class="mb-6">
        <a href="{{ route($prefix . 'cameras.index') }}"
            class="inline-flex items-center gap-2 text-gray-500 hover:text-indigo-600 transition-colors duration-200 font-medium group">
            <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm group-hover:shadow-md transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </div>
            <span>Volver al listado</span>
        </a>
    </div>

    {{-- Tarjeta Principal --}}
    <div class="glass-panel bg-white/80 backdrop-blur-xl border border-white/50 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative">

        {{-- Decoración superior --}}
        <div class="h-2 w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

        <div class="p-8 sm:p-10">

            {{-- Encabezado del Formulario --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="p-3 bg-indigo-50 rounded-2xl text-indigo-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Registrar Cámara</h1>
                    <p class="text-gray-500 text-sm">Configura los datos de conexión del nuevo dispositivo.</p>
                </div>
            </div>

            {{-- FORMULARIO --}}
            <form method="POST" action="{{ route($prefix . 'cameras.store') }}">
                @csrf

                <div class="space-y-6">

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre del Dispositivo</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <input type="text" name="name" required
                                class="w-full pl-11 pr-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 placeholder-gray-400"
                                placeholder="Ej: Cámara Entrada Norte">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">IP o Enlace</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                </div>
                                <input type="text" name="ip" required
                                    class="w-full pl-11 pr-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 font-mono text-sm placeholder-gray-400"
                                    placeholder="Ej: 192.168.1.50">
                            </div>
                            <p class="text-xs text-gray-500 mt-1 ml-1">
                                Si es celular, solo la IP (ej: 192.168.1.20)
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Estatus Inicial</label>
                            <div class="relative">
                                <select name="status" class="w-full pl-4 pr-10 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 appearance-none cursor-pointer">
                                    <option value="1">Activa</option>
                                    <option value="0">Inactiva</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ubicación / Zona</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <input type="text" name="location"
                                class="w-full pl-11 pr-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 placeholder-gray-400"
                                placeholder="Ej: Pasillo Planta Baja">
                        </div>
                    </div>

                    {{-- 5. ASIGNAR DUEÑO (SOLO VISIBLE PARA ADMIN) --}}
                    @if($userRole === 'admin' && isset($usersByRole) && count($usersByRole) > 0)
                        <div class="pt-4 border-t border-gray-100">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Asignar Propietario</label>
                            <div class="relative">
                                <select name="user_id" class="w-full pl-4 pr-10 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 cursor-pointer">
                                    
                                    {{-- Opción por defecto --}}
                                    <option value="{{ Auth::id() }}" class="font-bold text-indigo-600">
                                        -- Asignar a mí mismo (Admin) --
                                    </option>

                                    {{-- Iteramos los roles en orden --}}
                                    @foreach($priorityOrder as $roleKey)
                                        @if(isset($usersByRole[$roleKey]) && $usersByRole[$roleKey]->count() > 0)
                                            <optgroup label="{{ $roleLabels[$roleKey] }}">
                                                @foreach($usersByRole[$roleKey] as $u)
                                                    {{-- Evitamos mostrar al admin actual en la lista para no duplicar --}}
                                                    @if($u->id !== Auth::id())
                                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                                    @endif
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach

                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">El usuario seleccionado será quien vea esta cámara en su panel.</p>
                        </div>
                    @else
                        {{-- CASO MANTENIMIENTO: Se auto-asigna ocultamente --}}
                        <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                    @endif

                    <div class="pt-4">
                        <button class="w-full py-3.5 px-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl shadow-lg shadow-indigo-500/30 transform transition hover:-translate-y-1 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Registrar Dispositivo
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection