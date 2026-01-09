@extends('layouts.qmatic')

@section('title', $settings['name'] . ' - Borne Ticket')

@section('content')
<div class="min-h-screen flex flex-col qmatic-kiosk-root" style="background-color: {{ $settings['bg_color'] }}; color: {{ $settings['text_color'] }}">
    <!-- Header Minimaliste (Sans Navbar) -->
    <header class="p-6 flex justify-between items-center bg-white shadow-sm no-print" style="background-color: {{ $settings['card_bg_color'] }}">
        <div class="flex items-center gap-4">
            @if($settings['logo'])
                <img src="{{ $settings['logo'] }}" alt="Logo" class="h-16 w-auto object-contain">
            @endif
            <div>
                <h1 class="text-2xl font-bold">{{ $settings['name'] }}</h1>
                <p class="text-gray-500">{{ $settings['welcome'] }}</p>
            </div>
        </div>
        <div class="text-right">
            <div class="text-3xl font-bold" id="current-time"></div>
            <div class="text-sm text-gray-400">{{ now()->translatedFormat('l d F Y') }}</div>
        </div>
    </header>

    <!-- Sélection des Services -->
    <main class="flex-grow p-8 max-w-7xl mx-auto w-full">
        <div class="mb-10 text-center">
            <h2 class="text-4xl font-black mb-2">Choisissez votre service</h2>
            <p class="text-xl opacity-75">Appuyez sur le bouton correspondant pour obtenir votre ticket</p>
        </div>

        @if($settings['layout'] === 'list')
            <!-- Layout Liste (Ligne par ligne) -->
            <div class="space-y-4">
                @foreach($services as $service)
                    <form action="{{ route('qmatic.kiosk.generate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <button type="submit" class="w-full flex items-center justify-between p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all border-l-8 active:scale-[0.98]" 
                            style="background-color: {{ $settings['card_bg_color'] }}; border-color: {{ $service->color ?? $settings['color'] }}">
                            <div class="flex items-center gap-6">
                                <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl text-white shadow-inner" style="background-color: {{ $service->color ?? $settings['color'] }}">
                                    @if($service->icon)
                                        <i class="{{ $service->icon }}"></i>
                                    @else
                                        {{ substr($service->name, 0, 1) }}
                                    @endif
                                </div>
                                <div class="text-left">
                                    <h3 class="text-2xl font-bold">{{ $service->name }}</h3>
                                    <p class="opacity-60">{{ $service->description ?? 'Obtenir un ticket pour ce service' }}</p>
                                </div>
                            </div>
                            <div class="text-4xl opacity-20">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </button>
                    </form>
                @endforeach
            </div>
        @elseif($settings['layout'] === 'large_cards')
            <!-- Layout Grandes Cartes -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($services as $service)
                    <form action="{{ route('qmatic.kiosk.generate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <button type="submit" class="w-full h-64 rounded-3xl shadow-xl hover:shadow-2xl transition-all border-b-8 overflow-hidden relative group active:scale-[0.98]" 
                            style="background-color: {{ $settings['card_bg_color'] }}; border-color: {{ $service->color ?? $settings['color'] }}">
                            @if($service->image_url)
                                <div class="absolute inset-0 opacity-10 group-hover:opacity-20 transition-opacity">
                                    <img src="{{ $service->image_url }}" alt="" class="w-full h-full object-cover">
                                </div>
                            @endif
                            <div class="relative z-10 flex flex-col items-center justify-center h-full p-6">
                                <div class="w-24 h-24 rounded-full flex items-center justify-center text-4xl text-white mb-4 shadow-lg" style="background-color: {{ $service->color ?? $settings['color'] }}">
                                    <i class="{{ $service->icon ?? 'fas fa-ticket-alt' }}"></i>
                                </div>
                                <h3 class="text-3xl font-black text-center">{{ $service->name }}</h3>
                            </div>
                        </button>
                    </form>
                @endforeach
            </div>
        @else
            <!-- Layout Grille Standard -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                    <form action="{{ route('qmatic.kiosk.generate') }}" method="POST">
                        @csrf
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <button type="submit" class="w-full p-8 rounded-3xl shadow-lg hover:shadow-2xl transition-all border-t-4 active:scale-[0.98] flex flex-col items-center text-center" 
                            style="background-color: {{ $settings['card_bg_color'] }}; border-color: {{ $service->color ?? $settings['color'] }}">
                            <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-3xl text-white mb-4 shadow-md rotate-3 group-hover:rotate-0 transition-transform" style="background-color: {{ $service->color ?? $settings['color'] }}">
                                <i class="{{ $service->icon ?? 'fas fa-concierge-bell' }}"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2">{{ $service->name }}</h3>
                            <p class="text-sm opacity-60 line-clamp-2">{{ $service->description }}</p>
                        </button>
                    </form>
                @endforeach
            </div>
        @endif
    </main>

    <!-- Footer Minimaliste -->
    <footer class="p-6 text-center opacity-50 text-sm">
        &copy; {{ date('Y') }} {{ $settings['name'] }} - Système de Gestion de File d'Attente
    </footer>
</div>

<style>
    /* Masquer la navbar globale */
    nav { display: none !important; }
    
    .qmatic-kiosk-root {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        overflow-y: auto;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<script>
    function updateTime() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
    }
    setInterval(updateTime, 1000);
    updateTime();
</script>
@endsection
