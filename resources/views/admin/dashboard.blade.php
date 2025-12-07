@extends('components.layouts.app')

@section('titulo','Dashboard Admin')

@section('contenido')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    {{-- ENCABEZADO CON BIENVENIDA --}}
    <div class="mb-10 flex flex-col md:flex-row md:items-end md:justify-between gap-4 fade-up">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Panel de Control
            </h1>
            <p class="text-gray-500 mt-1">
                Bienvenido de nuevo, <span class="font-medium text-indigo-600">{{ Auth::user()->name }}</span>. Aquí tienes el resumen del sistema.
            </p>
        </div>
        <div>
            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 shadow-sm">
                <span class="w-2 h-2 mr-2 bg-indigo-500 rounded-full animate-pulse"></span>
                Administrador
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12 fade-up" style="animation-delay: 0.1s;">
        
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:border-blue-200 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Personal</p>
                    <p class="text-4xl font-extrabold text-gray-900 mt-2">{{ $totalUsers }}</p>
                </div>
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-gray-500">
                <span class="text-green-500 font-medium flex items-center mr-2">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Activos
                </span>
                <span>Usuarios registrados</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:border-indigo-200 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">Dispositivos</p>
                    <p class="text-4xl font-extrabold text-gray-900 mt-2">{{ $totalCameras }}</p>
                </div>
                <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-gray-500">
                <span>Total de cámaras en sistema</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:border-green-200 transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-400 uppercase tracking-wide">En Línea</p>
                    <p class="text-4xl font-extrabold text-gray-900 mt-2">{{ $activeCameras }}</p>
                </div>
                <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 group-hover:scale-110 group-hover:bg-green-600 group-hover:text-white transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-gray-500">
                <span class="text-green-600 font-bold bg-green-100 px-2 py-0.5 rounded text-xs mr-2">LIVE</span>
                <span>Transmisión estable</span>
            </div>
        </div>
    </div>

    <div class="fade-up" style="animation-delay: 0.2s;">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            Gestión Rápida
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <a href="{{ route('admin.personal.index') }}" class="group relative bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-[100px] -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-4 shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Ver Personal</h3>
                    <p class="text-sm text-gray-500 mt-1">Administrar usuarios y roles</p>
                </div>
            </a>

            <a href="{{ route('admin.personal.create') }}" class="group relative bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-[100px] -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-blue-50 text-blue-500 border border-blue-200 rounded-xl flex items-center justify-center mb-4 shadow-sm group-hover:bg-blue-600 group-hover:text-white group-hover:border-transparent transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors">Registrar Usuario</h3>
                    <p class="text-sm text-gray-500 mt-1">Alta de nuevo personal</p>
                </div>
            </a>

            <a href="{{ route('admin.cameras.index') }}" class="group relative bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-bl-[100px] -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center mb-4 shadow-sm group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">Monitor de Cámaras</h3>
                    <p class="text-sm text-gray-500 mt-1">Visualizar transmisiones</p>
                </div>
            </a>

            <a href="{{ route('admin.cameras.create') }}" class="group relative bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-bl-[100px] -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-500 border border-indigo-200 rounded-xl flex items-center justify-center mb-4 shadow-sm group-hover:bg-indigo-600 group-hover:text-white group-hover:border-transparent transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">Instalar Dispositivo</h3>
                    <p class="text-sm text-gray-500 mt-1">Configurar nueva cámara</p>
                </div>
            </a>

        </div>
    </div>

</div>

@endsection