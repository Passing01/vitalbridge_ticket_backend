<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Qmatic - VitalBridge')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }
        
        .qmatic-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        
        .qmatic-header {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 1.5rem;
        }
        
        .btn-qmatic {
            background: #2563eb;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-qmatic:hover {
            background: #1d4ed8;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-waiting { background: #e0f2fe; color: #0369a1; }
        .status-called { background: #fef3c7; color: #b45309; }
        .status-serving { background: #dcfce7; color: #15803d; }
        .status-served { background: #f3f4f6; color: #374151; }
        .status-absent { background: #fee2e2; color: #b91c1c; }
        
        .priority-urgent { color: #dc2626; font-weight: 700; }
        .priority-vip { color: #7c3aed; font-weight: 600; }
        .priority-senior { color: #059669; font-weight: 500; }
        
        @yield('styles')
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-xl font-bold text-blue-600 flex items-center gap-2">
                                🎫 VitalBridge <span class="text-gray-400 font-normal">| Qmatic</span>
                            </a>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            @if(auth()->guard('qmatic')->check())
                            <a href="{{ route('qmatic.agent.dashboard') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Interface Agent
                            </a>
                            @endif
                            
                            @if(auth()->check() && (auth()->user()->role === 'admin' || auth()->user()->role === 'reception'))
                            <a href="{{ route('qmatic.admin.services.index') }}" class="{{ request()->routeIs('qmatic.admin.services.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500' }} hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Services
                            </a>
                            <a href="{{ route('qmatic.admin.users.index') }}" class="{{ request()->routeIs('qmatic.admin.users.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500' }} hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Agents
                            </a>
                            <a href="{{ route('qmatic.admin.counters.index') }}" class="{{ request()->routeIs('qmatic.admin.counters.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500' }} hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Guichets
                            </a>
                            <a href="{{ route('qmatic.admin.settings.index') }}" class="{{ request()->routeIs('qmatic.admin.settings.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500' }} hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Paramètres
                            </a>
                            @endif
                            
                            <a href="{{ route('qmatic.kiosk.index') }}" target="_blank" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Borne Ticket
                            </a>
                            <a href="{{ route('qmatic.display.index') }}" target="_blank" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Écran Public
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="ml-3 relative">
                            <div class="flex items-center gap-3">
                                @if(auth()->guard('qmatic')->check())
                                    <span class="text-sm text-gray-700">{{ auth()->guard('qmatic')->user()->name }} (Agent)</span>
                                    <form method="POST" action="{{ route('qmatic.logout') }}">
                                        @csrf
                                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Déconnexion</button>
                                    </form>
                                @elseif(auth()->check())
                                    <span class="text-sm text-gray-700">{{ auth()->user()->name }}</span>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Déconnexion</button>
                                    </form>
                                @else
                                    <a href="{{ route('qmatic.login') }}" class="text-sm text-blue-600 hover:text-blue-800">Connexion Agent</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="flex-1 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    @foreach($errors->all() as $error)
                                        <span class="block">{{ $error }}</span>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
    
    @yield('scripts')
</body>
</html>
