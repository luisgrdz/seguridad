@extends('components.layouts.app')

@section('titulo', 'Cámaras')

@section('contenido')

@php
    $userRole = Auth::user()->role->name ?? 'user';
    $prefix = match($userRole) {
        'admin' => 'admin.',
        'supervisor' => 'supervisor.',
        'mantenimiento' => 'mantenimiento.',
        default => 'user.',
    };
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Mis Cámaras</h1>
            <p class="text-gray-500 mt-1 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500"></span> {{ $cameras->where('status', true)->count() }} Online
                <span class="w-2 h-2 rounded-full bg-red-500 ml-2"></span> {{ $cameras->where('status', false)->count() }} Offline
            </p>
        </div>

        @can('create', App\Models\Camera::class)
            <a href="{{ route($prefix . 'cameras.create') }}" class="group inline-flex items-center justify-center px-5 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-xl hover:bg-black transition-all shadow-lg shadow-gray-500/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Cámara
            </a>
        @endcan
    </div>

    {{-- GRID DE CÁMARAS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        
        @forelse($cameras as $camera)
            @can('view', $camera)
            <div class="group relative bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-xl hover:border-indigo-100 transition-all duration-300">
                
                {{-- Estado (Barra superior de color) --}}
                <div class="h-1.5 w-full {{ $camera->status ? 'bg-green-500' : 'bg-red-500' }}"></div>

                <div class="p-5">
                    {{-- Encabezado Card --}}
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2.5 bg-gray-50 rounded-xl text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                        
                        {{-- Badge Estado --}}
                        <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold uppercase tracking-wide {{ $camera->status ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                            {{ $camera->status ? 'Online' : 'Offline' }}
                        </span>
                    </div>

                    {{-- Info Principal --}}
                    <h3 class="text-lg font-bold text-gray-900 mb-1 truncate">{{ $camera->name }}</h3>
                    <p class="text-sm text-gray-500 mb-4 flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ $camera->location ?? 'Sin ubicación' }}
                    </p>

                    {{-- Detalles Técnicos --}}
                    <div class="bg-gray-50 rounded-lg p-3 text-xs text-gray-500 space-y-1 mb-4 border border-gray-100">
                        <div class="flex justify-between">
                            <span>IP:</span>
                            <span class="font-mono text-gray-700">{{ $camera->ip }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Encargado:</span>
                            <span class="font-medium text-gray-700 truncate max-w-[100px]">{{ $camera->user->name ?? '—' }}</span>
                        </div>
                    </div>

                    {{-- Botón Principal --}}
                    <a href="{{ route($prefix . 'cameras.show', $camera) }}" class="block w-full text-center bg-white border border-gray-300 text-gray-700 font-medium py-2 rounded-lg hover:bg-gray-50 hover:text-indigo-600 transition-colors">
                        Ver Monitor
                    </a>
                </div>

                {{-- Acciones Admin (Overlay al Hover) --}}
                @can('update', $camera)
                    <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                        <a href="{{ route($prefix . 'cameras.edit', $camera) }}" class="p-1.5 bg-white text-gray-500 rounded-full shadow hover:text-blue-600" title="Editar">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                        
                        @can('delete', $camera)
                            <form action="{{ route('admin.cameras.destroy', $camera) }}" method="POST" onsubmit="return confirm('¿Eliminar?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 bg-white text-gray-500 rounded-full shadow hover:text-red-600" title="Eliminar">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        @endcan
                    </div>
                @endcan

            </div>
            @endcan
        @empty
            <div class="col-span-full py-16 text-center">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No se encontraron cámaras</h3>
                <p class="mt-1 text-gray-500">
                    @if($userRole === 'admin' || $userRole === 'mantenimiento')
                        Registra un nuevo dispositivo para comenzar.
                    @else
                        No tienes dispositivos asignados.
                    @endif
                </p>
            </div>
        @endforelse

    </div>

    {{-- Paginación --}}
    @if($cameras->hasPages())
        <div class="mt-8">
            {{ $cameras->links() }}
        </div>
    @endif

</div>

@endsection