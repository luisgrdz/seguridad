@extends('components.layouts.app')

@section('titulo', 'Editar Usuario')

@section('contenido')

<div class="max-w-2xl mx-auto">
    
    {{-- Botón Volver --}}
    <div class="mb-6">
        <a href="{{ route('admin.personal.index') }}" class="inline-flex items-center gap-2 text-gray-500 hover:text-indigo-600 transition-colors duration-200 font-medium group">
            <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm group-hover:shadow-md transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </div>
            <span>Volver al personal</span>
        </a>
    </div>

    {{-- Tarjeta Principal --}}
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden relative">
        
        {{-- Decoración superior --}}
        <div class="h-2 w-full bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

        <div class="p-8 sm:p-10">
            
            {{-- Encabezado --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="p-3 bg-blue-50 rounded-2xl text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Editar Usuario</h1>
                    <p class="text-gray-500 text-sm">Actualiza la información y permisos de <span class="font-semibold">{{ $user->name }}</span>.</p>
                </div>
            </div>

            {{-- Formulario --}}
            <form method="POST" action="{{ route('admin.personal.update', $user) }}">
                @csrf
                @method('PATCH')

                <div class="space-y-6">
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre Completo</label>
                        <div class="relative">
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 placeholder-gray-400">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Correo Electrónico</label>
                        <div class="relative">
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 placeholder-gray-400">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Rol del Usuario</label>
                            <div class="relative">
                                <select name="role_id" id="role_id" onchange="toggleSupervisor()"
                                    class="w-full pl-4 pr-10 py-3 rounded-xl bg-white border border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 appearance-none cursor-pointer shadow-sm">
                                    @foreach($roles as $role)
                                        @php
                                            // Traducción manual para el Select
                                            $roleName = match($role->name) {
                                                'admin' => 'Administrador General',
                                                'supervisor' => 'Supervisor de Zona',
                                                'mantenimiento' => 'Técnico de Soporte',
                                                'user' => 'Guardia de Seguridad',
                                                default => ucfirst($role->name),
                                            };
                                        @endphp
                                        <option value="{{ $role->id }}" {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                            {{ $roleName }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-500">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        <div id="div_supervisor">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Supervisor Asignado</label>
                            <div class="relative">
                                <select name="supervisor_id" 
                                    class="w-full pl-4 pr-10 py-3 rounded-xl bg-white border border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 appearance-none cursor-pointer shadow-sm">
                                    <option value="">-- Sin Supervisor --</option>
                                    @foreach($supervisors as $sup)
                                        <option value="{{ $sup->id }}" {{ $user->supervisor_id == $sup->id ? 'selected' : '' }}>
                                            {{ $sup->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-500">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">El supervisor podrá ver las cámaras de este usuario.</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nueva Contraseña <span class="text-gray-400 font-normal">(Opcional)</span></label>
                        <input type="password" name="password" 
                            class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 placeholder-gray-400" 
                            placeholder="Dejar en blanco para mantener la actual">
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transform transition hover:-translate-y-1 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleSupervisor() {
        const roleSelect = document.getElementById('role_id');
        const supervisorDiv = document.getElementById('div_supervisor');
        
        // Obtenemos el texto de la opción seleccionada y lo pasamos a minúsculas
        const selectedText = roleSelect.options[roleSelect.selectedIndex].text.toLowerCase();
        
        // Lógica actualizada: Si dice 'administrador' o 'supervisor', ocultamos
        if (selectedText.includes('administrador') || selectedText.includes('supervisor')) {
            supervisorDiv.style.display = 'none';
        } else {
            supervisorDiv.style.display = 'block';
        }
    }

    // Ejecutar al cargar para establecer el estado inicial correcto
    document.addEventListener('DOMContentLoaded', toggleSupervisor);
</script>

@endsection