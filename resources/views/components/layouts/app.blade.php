<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo', 'Videovigilancia')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #eef2ff 0%, #f5f3ff 50%, #e0e7ff 100%);
            min-height: 100vh;
            color: #4b5563;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.07);
        }

        .nav-glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.6);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        /* Enlaces del Nav */
        .nav-link-custom {
            font-weight: 500;
            color: #6b7280;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .nav-link-custom:hover, .nav-link-custom.active {
            color: #4f46e5; /* Indigo 600 */
            background: rgba(255, 255, 255, 0.6);
            transform: translateY(-1px);
        }

        /* Botón Logout con degradado */
        .btn-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            color: white;
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            border-radius: 9999px;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
            filter: brightness(1.05);
        }

        main {
            animation: fadeUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>

    <div id="logout-overlay" class="fixed inset-0 bg-white/60 backdrop-blur-sm z-[100] hidden flex-col justify-center items-center transition-all duration-300">
        <div class="relative">
            <div class="w-16 h-16 border-4 border-indigo-200 border-dashed rounded-full animate-spin"></div>
            <div class="absolute top-0 left-0 w-16 h-16 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
        </div>
        <h2 class="mt-4 text-lg font-semibold text-indigo-700 animate-pulse">Cerrando sesión...</h2>
    </div>

    <nav class="nav-glass shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">

                <div class="flex items-center gap-2">
                    <span class="text-3xl">📹</span>
                    <div class="flex flex-col">
                        <span class="font-bold text-xl bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent leading-tight">
                            Videovigilancia
                        </span>
                        <span class="text-[10px] text-gray-400 font-medium tracking-widest uppercase">Security System</span>
                    </div>
                </div>

                @auth
                <div class="flex items-center gap-4 sm:gap-6">
                    
                    @php
                        $userRole = Auth::user()->role->name ?? 'user';
                        
                        $dashboardRoute = match($userRole) {
                            'admin' => route('admin.dashboard'),
                            'supervisor' => route('supervisor.dashboard'),
                            'mantenimiento' => route('mantenimiento.dashboard'),
                            default => route('user.dashboard'),
                        };

                        $camerasRoute = match($userRole) {
                            'admin' => route('admin.cameras.index'),
                            'supervisor' => route('supervisor.cameras.index'),
                            'mantenimiento' => route('mantenimiento.cameras.index'),
                            default => route('user.cameras.index'),
                        };
                    @endphp

                    <a href="{{ $dashboardRoute }}" class="nav-link-custom hidden sm:block {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                        Inicio
                    </a>

                    <a href="{{ $camerasRoute }}" class="nav-link-custom hidden sm:block {{ request()->routeIs('*.cameras.*') ? 'active' : '' }}">
                        Cámaras
                    </a>

                    @if($userRole === 'admin')
                        <a href="{{ route('admin.personal.index') }}" class="nav-link-custom hidden sm:block {{ request()->routeIs('admin.personal.*') ? 'active' : '' }}">
                            Personal
                        </a>
                    @endif

                    <div class="h-6 w-px bg-gray-300 hidden sm:block"></div>

                    <div class="flex items-center gap-4">
                        
                        @if($userRole !== 'user')
                            @php
                                // Contar pendientes (simple query para la vista)
                                $pendingCount = \App\Models\Incident::where('status', 'pendiente')->count();
                            @endphp
                            
                            <a href="{{ route('incidents.index') }}" class="relative p-2 text-gray-500 hover:text-indigo-600 hover:bg-gray-100 rounded-full transition-colors" title="Incidencias">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                
                                @if($pendingCount > 0)
                                    <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white animate-pulse">
                                        {{ $pendingCount }}
                                    </span>
                                @endif
                            </a>
                        @endif

                        <div class="text-right hidden sm:block leading-tight">
                            <span class="block text-sm font-medium text-gray-700">
                                {{ Auth::user()->name }}
                            </span>
                            <span class="block text-[10px] text-gray-500 uppercase font-bold tracking-wide">
                                {{ $userRole }}
                            </span>
                        </div>
                        
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-gradient text-sm">
                                Salir
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        
        @if(session('success'))
            <div class="mb-6 glass-panel border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-r relative flex items-center gap-3" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @yield('contenido')
    </main>

    <script>
        const logoutForm = document.getElementById('logout-form');
        if(logoutForm) {
            logoutForm.addEventListener('submit', function(e) {
                e.preventDefault(); 
                const overlay = document.getElementById('logout-overlay');
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');
                setTimeout(() => { this.submit(); }, 300); 
            });
        }
    </script>

</body>
</html>