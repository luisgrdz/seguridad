@extends('components.layouts.app')

@section('titulo','Crear usuario')

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
        
        {{-- Decoración --}}
        <div class="h-2 w-full bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500"></div>

        <div class="p-8 sm:p-10">
            
            {{-- Encabezado --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="p-3 bg-blue-50 rounded-2xl text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Registrar Nuevo Usuario</h1>
                    <p class="text-gray-500 text-sm">Crea una cuenta para el personal de seguridad.</p>
                </div>
            </div>

            {{-- Formulario --}}
            <form method="POST" action="{{ route('admin.personal.store') }}">
                @csrf

                <div class="space-y-6">
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre Completo</label>
                        <div class="relative">
                            <input type="text" name="name" required 
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 placeholder-gray-400" 
                                placeholder="Ej: Juan Pérez">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Correo Electrónico</label>
                        <div class="relative">
                            <input type="email" name="email" required 
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 placeholder-gray-400" 
                                placeholder="usuario@empresa.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Contraseña de Acceso</label>
                        <div class="relative">
                            <input type="password" name="password" required 
                                class="w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 placeholder-gray-400" 
                                placeholder="••••••••">
                        </div>
                        <p class="text-xs text-gray-400 mt-2 ml-1">Se recomienda usar al menos 8 caracteres.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Rol del Usuario</label>
                            <div class="relative">
                                <select name="role_id" id="role_id" onchange="toggleSupervisor()"
                                    class="w-full pl-4 pr-10 py-3 rounded-xl bg-white border border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all outline-none text-gray-700 appearance-none cursor-pointer shadow-sm">
                                    @foreach($roles as $role)
                                        @php
                                            $roleName = match($role->name) {
                                                'admin' => 'Administrador General',
                                                'supervisor' => 'Supervisor de Zona',
                                                'mantenimiento' => 'Técnico de Soporte',
                                                'user' => 'Guardia de Seguridad',
                                                default => ucfirst($role->name),
                                            };
                                        @endphp
                                        <option value="{{ $role->id }}">{{ $roleName }}</option>
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
                                        <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-gray-500">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">El supervisor podrá ver las cámaras de este usuario.</p>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transform transition hover:-translate-y-1 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Crear Usuario
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
        
        // Si es Admin o Supervisor, ocultamos el campo de jefe
        if (selectedText.includes('administrador') || selectedText.includes('supervisor')) {
            supervisorDiv.style.display = 'none';
        } else {
            supervisorDiv.style.display = 'block';
        }
    }

    // Ejecutar al cargar para establecer el estado inicial
    document.addEventListener('DOMContentLoaded', toggleSupervisor);
</script>

@endsection