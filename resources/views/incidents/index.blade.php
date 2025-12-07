@extends('components.layouts.app')

@section('titulo', 'Centro de Incidencias')

@section('contenido')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">

    {{-- HEADER --}}
    <div class="flex justify-between items-end mb-6 border-b border-gray-200 pb-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bandeja de Incidencias</h1>
            <p class="text-sm text-gray-500 mt-1">Reportes de fallos y alertas de seguridad</p>
        </div>
        
        {{-- Resumen rápido (Estética) --}}
        <div class="flex gap-3">
            <div class="px-4 py-2 bg-red-50 text-red-700 rounded-lg text-sm font-medium border border-red-100">
                Pendientes: {{ $incidents->where('status', 'pendiente')->count() }}
            </div>
        </div>
    </div>

    {{-- TABLA DE INCIDENCIAS --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Prioridad</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Detalle del Problema</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Dispositivo</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Reportado Por</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acción</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($incidents as $incident)
                        <tr class="hover:bg-gray-50 transition-colors">
                            
                            {{-- PRIORIDAD --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $prioColor = match($incident->priority) {
                                        'critica' => 'bg-red-100 text-red-800 border-red-200',
                                        'alta' => 'bg-orange-100 text-orange-800 border-orange-200',
                                        'media' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        default => 'bg-gray-100 text-gray-600 border-gray-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $prioColor }} uppercase">
                                    {{ $incident->priority }}
                                </span>
                            </td>

                            {{-- DETALLE --}}
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $incident->type }}</div>
                                <div class="text-xs text-gray-500 mt-0.5 line-clamp-1" title="{{ $incident->description }}">
                                    {{ $incident->description }}
                                </div>
                                <div class="text-[10px] text-gray-400 mt-1">
                                    {{ $incident->created_at->format('d M Y - H:i') }} (Hace {{ $incident->created_at->diffForHumans() }})
                                </div>
                            </td>

                            {{-- DISPOSITIVO --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <a href="{{ route(Auth::user()->role->name . '.cameras.show', $incident->camera_id) }}" class="text-sm text-indigo-600 hover:underline font-medium">
                                        {{ $incident->camera->name ?? 'Cámara Eliminada' }}
                                    </a>
                                </div>
                            </td>

                            {{-- USUARIO --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 mr-2">
                                        {{ substr($incident->user->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $incident->user->name ?? 'Usuario' }}</span>
                                </div>
                            </td>

                            {{-- ESTADO --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $statusColor = match($incident->status) {
                                        'pendiente' => 'bg-red-50 text-red-600',
                                        'en_revision' => 'bg-blue-50 text-blue-600',
                                        'resuelto' => 'bg-green-50 text-green-600',
                                        'cerrado' => 'bg-gray-100 text-gray-500 line-through',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                                </span>
                            </td>

                            {{-- ACCIONES (Cambiar Estado) --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                @if(in_array(Auth::user()->role->name, ['admin', 'supervisor']))
                                    <form action="{{ route('incidents.update', $incident) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" onchange="this.form.submit()" class="text-xs border-gray-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1 pl-2 pr-6 cursor-pointer bg-white hover:bg-gray-50 transition">
                                            <option value="pendiente" {{ $incident->status == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                            <option value="en_revision" {{ $incident->status == 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                                            <option value="resuelto" {{ $incident->status == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                                            <option value="cerrado" {{ $incident->status == 'cerrado' ? 'selected' : '' }}>Cerrar Ticket</option>
                                        </select>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">Solo lectura</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-12 w-12 bg-green-50 rounded-full flex items-center justify-center mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-medium text-gray-900">Todo en orden</h3>
                                    <p class="mt-1 text-sm text-gray-500">No hay incidencias pendientes de revisión.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación --}}
        @if($incidents->hasPages())
            <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                {{ $incidents->links() }}
            </div>
        @endif
    </div>
</div>
@endsection