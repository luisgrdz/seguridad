@extends('components.layouts.app')

@section('titulo', 'Monitor en Vivo')

@section('contenido')
<div class="max-w-7xl mx-auto mt-6 px-4">

    @php
        $userRole = Auth::user()->role->name ?? 'user';
        
        $prefix = match($userRole) {
            'admin' => 'admin.',
            'supervisor' => 'supervisor.',
            'mantenimiento' => 'mantenimiento.',
            default => 'user.',
        };
        
        $ip = trim($camera->ip);
        $streamUrl = '';
        $isYoutube = false;

        // --- 1. LÓGICA YOUTUBE ---
        if (str_contains($ip, 'youtube.com') || str_contains($ip, 'youtu.be')) {
            $isYoutube = true;
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $ip, $matches);
            $videoId = $matches[1] ?? null;
            
            // Parametros para "kiosko mode": autoplay, mute, sin controles, sin teclado, loop
            if($videoId) {
                $streamUrl = "https://www.youtube.com/embed/{$videoId}?autoplay=1&mute=1&controls=0&disablekb=1&modestbranding=1&showinfo=0&rel=0&loop=1&playlist={$videoId}";
            }
        } 
        // --- 2. LÓGICA IP WEBCAM (Celular Android) ---
        // Si tiene el puerto 8080 O si es una IP "pelada" (sin http ni puerto 81)
        elseif (str_contains($ip, ':8080') || (!str_contains($ip, 'http') && !str_contains($ip, ':81'))) {
            // Limpiamos por si acaso puso http o /video, queremos la IP pura
            $cleanIp = str_replace(['http://', 'https://', '/video'], '', $ip);
            // Reconstruimos la URL correcta para la APP
            $streamUrl = "http://{$cleanIp}:8080/video"; 
        } 
        // --- 3. OTROS (ESP32, Hikvision, etc) ---
        else {
            if (str_starts_with($ip, 'http')) {
                $streamUrl = $ip;
            } else {
                $streamUrl = "http://{$ip}:81/stream"; // Default ESP32
            }
        }
    @endphp

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-3xl font-bold text-gray-900">{{ $camera->name }}</h1>
                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $camera->status ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
                    {{ $camera->status ? 'EN LÍNEA' : 'OFFLINE' }}
                </span>
            </div>
            <p class="text-gray-500 text-sm mt-1">
                @if($isYoutube)
                    <span class="text-red-500 font-bold flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                        Simulación
                    </span> 
                @else
                    Visualización IP • ID: #{{ $camera->id }}
                @endif
            </p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route($prefix . 'cameras.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver
            </a>

            {{-- Botón Reportar (Nueva pestaña) --}}
            <a href="{{ route('incidents.create', $camera->id) }}" target="_blank" class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 font-medium transition-colors shadow-sm flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                Reportar Fallo
            </a>

            @can('update', $camera)
                <a href="{{ route($prefix . 'cameras.edit', $camera) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium shadow-md transition-all flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Configurar
                </a>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- COLUMNA IZQUIERDA: VIDEO --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-black rounded-xl overflow-hidden shadow-xl border border-gray-800 relative group aspect-video flex items-center justify-center">
                
                @if($camera->status)
                    
                    {{-- SI ES YOUTUBE --}}
                    @if($isYoutube)
                        <div class="absolute inset-0 z-20 w-full h-full bg-transparent"></div>
                        
                        <iframe 
                            src="{{ $streamUrl }}" 
                            title="Cam Feed" 
                            class="w-full h-full pointer-events-none" {{-- pointer-events-none extra seguridad --}}
                            frameborder="0" 
                            allow="autoplay; encrypted-media;">
                        </iframe>
                    
                    {{-- SI ES CAMARA REAL (IP WEBCAM) --}}
                    @else
                        <img 
                            id="live-stream"
                            src="{{ $streamUrl }}" 
                            class="w-full h-full object-contain bg-black"
                            alt="Video en vivo"
                            onerror="handleVideoError(this)"
                        >
                        {{-- Error Overlay --}}
                        <div id="video-error" class="hidden absolute inset-0 flex flex-col items-center justify-center bg-gray-900 text-white p-6 text-center z-10">
                            <h3 class="text-lg font-bold text-red-500">Sin Señal</h3>
                            <p class="text-gray-400 text-sm mb-4">Verifique que la App IP Webcam esté iniciada en la IP: <br><span class="font-mono text-yellow-400">{{ $ip }}</span></p>
                            <button onclick="location.reload()" class="px-4 py-2 bg-gray-800 rounded text-sm hover:bg-gray-700">Reintentar</button>
                        </div>
                    @endif

                    {{-- Indicador LIVE (común para ambos) --}}
                    <div class="absolute top-4 left-4 flex gap-2 z-30 pointer-events-none">
                        <span class="bg-red-600/90 text-white text-[10px] font-bold px-2 py-0.5 rounded animate-pulse shadow-lg backdrop-blur-sm">● LIVE</span>
                        <span class="bg-black/50 text-white text-[10px] font-mono px-2 py-0.5 rounded backdrop-blur-sm">{{ date('H:i:s') }}</span>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-16 h-16 mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <span class="text-lg font-medium">Cámara Desconectada</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- COLUMNA DERECHA: INFO --}}
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-5 py-3 border-b border-gray-200">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Detalles del Dispositivo</h3>
                </div>
                
                <div class="p-5 space-y-5">
                    
                    {{-- Ubicación --}}
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase">Ubicación Física</p>
                            <p class="text-gray-900 font-medium">{{ $camera->location ?? 'No especificada' }}</p>
                        </div>
                    </div>

                    {{-- Zona --}}
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-purple-50 text-purple-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase">Zona / Grupo</p>
                            <p class="text-gray-900 font-medium">{{ $camera->group ?? 'General' }}</p>
                        </div>
                    </div>

                    {{-- IP --}}
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-gray-50 text-gray-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                        </div>
                        <div class="overflow-hidden w-full">
                            <p class="text-xs text-gray-500 font-bold uppercase">Enlace Fuente</p>
                            <a href="{{ $isYoutube ? $ip : $streamUrl }}" target="_blank" class="text-sm text-blue-600 hover:underline truncate block" title="{{ $ip }}">
                                {{ Str::limit($ip, 25) }}
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function handleVideoError(img) {
        // Solo mostramos error si NO es YouTube
        document.getElementById('video-error').classList.remove('hidden');
        img.style.display = 'none';
    }
</script>

@endsection