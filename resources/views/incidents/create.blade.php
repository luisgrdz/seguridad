@extends('components.layouts.app')

@section('titulo', 'Reportar Incidencia')

@section('contenido')
<div class="max-w-2xl mx-auto mt-10">
    
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        
        {{-- Header Rojo de Alerta --}}
        <div class="bg-red-50 px-8 py-6 border-b border-red-100 flex items-center gap-4">
            <div class="bg-red-100 p-3 rounded-full text-red-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">Reportar Fallo Técnico</h2>
                <p class="text-sm text-red-600 font-medium">Dispositivo: {{ $camera->name }} ({{ $camera->ip }})</p>
            </div>
        </div>

        <form action="{{ route('incidents.store') }}" method="POST" class="p-8 space-y-6">
            @csrf
            {{-- ID Oculto de la cámara --}}
            <input type="hidden" name="camera_id" value="{{ $camera->id }}">

            {{-- Fila 1: Tipo y Prioridad --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de Incidencia</label>
                    <select name="type" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm p-2.5 bg-gray-50">
                        <option value="Sin Señal">Sin Señal / Pantalla Negra</option>
                        <option value="Imagen Defectuosa">Imagen Borrosa / Con Ruido</option>
                        <option value="Conexión Intermitente">Conexión Intermitente</option>
                        <option value="Daño Físico">Cámara Rota / Vandalismo</option>
                        <option value="Otro">Otro Problema</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nivel de Urgencia</label>
                    <select name="priority" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm p-2.5 bg-gray-50">
                        <option value="baja">Baja (No afecta operación)</option>
                        <option value="media" selected>Media (Afecta visibilidad)</option>
                        <option value="alta">Alta (Zona crítica sin cobertura)</option>
                        <option value="critica">Crítica (Riesgo de seguridad inminente)</option>
                    </select>
                </div>
            </div>

            {{-- Fila 2: Descripción --}}
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Descripción Detallada</label>
                <textarea name="description" rows="4" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 shadow-sm p-3" placeholder="Describe qué observaste, desde qué hora sucede, etc..."></textarea>
            </div>

            {{-- Botones --}}
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                {{-- Botón Cancelar (Cierra la pestaña si se abrió en una nueva, o vuelve atrás) --}}
                <a href="{{ url()->previous() }}" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                
                <button type="submit" class="px-6 py-2.5 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 shadow-lg shadow-red-500/30 transform hover:-translate-y-0.5 transition-all">
                    Enviar Reporte
                </button>
            </div>
        </form>
    </div>
</div>
@endsection