@extends('layouts.qmatic')

@section('title', $settings['name'] . ' - Écran Public')

@section('content')
@php
    $layout = $settings['display_layout'] ?? 'sidebar_right';
    $bgColor = $settings['display_bg_color'] ?? '#111827';
    $secondaryColor = $settings['display_secondary_color'] ?? '#1f2937';
    $textColor = $settings['display_text_color'] ?? '#ffffff';
@endphp

<div class="min-h-screen flex flex-col overflow-hidden qmatic-display-root" style="background-color: {{ $bgColor }}; color: {{ $textColor }}; --primary-color: {{ $settings['color'] }}; --secondary-color: {{ $secondaryColor }}">
    
    <!-- Header -->
    <header class="p-4 flex justify-between items-center border-b-2 no-print" style="background-color: {{ $secondaryColor }}; border-color: var(--primary-color)">
        <div class="flex items-center gap-4">
            @if($settings['logo'])
                <img src="{{ $settings['logo'] }}" alt="Logo" class="h-12 w-auto object-contain bg-white p-1 rounded">
            @endif
            <h1 class="text-2xl font-bold">{{ $settings['name'] }}</h1>
        </div>
        <div class="text-right flex items-center gap-6">
            <div class="text-sm text-gray-400" id="current-date">{{ now()->translatedFormat('l d F Y') }}</div>
            <div class="text-3xl font-mono font-bold" id="current-time"></div>
            <button onclick="toggleFullScreen()" class="p-2 hover:bg-gray-700 rounded-lg transition-colors">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </header>

    <!-- Main Layout Container -->
    <main class="flex-grow flex overflow-hidden relative p-4 gap-4 {{ $layout === 'sidebar_left' ? 'flex-row-reverse' : ($layout === 'compact_bottom' ? 'flex-col' : 'flex-row') }}">
        
        <!-- Zone Principale (Vidéos / Appel en cours) -->
        <div class="{{ $layout === 'compact_bottom' ? 'flex-grow' : 'w-3/4' }} flex flex-col gap-4">
            <!-- Appel Actuel (Grand) -->
            <div class="flex-grow rounded-3xl p-8 flex flex-col items-center justify-center border-4 border-gray-700 shadow-2xl relative overflow-hidden" style="background-color: var(--secondary-color)">
                <div class="absolute top-0 left-0 w-full h-2" style="background-color: var(--primary-color)"></div>
                
                @if($recentCalls->count() > 0)
                    @php $lastCall = $recentCalls->first(); @endphp
                    <div class="text-center" id="main-call-container">
                        <div class="text-3xl opacity-60 uppercase tracking-widest mb-2">Ticket</div>
                        <div class="text-[12rem] font-black leading-none mb-4 animate-pulse-slow" style="color: var(--primary-color)">{{ $lastCall->ticket_number }}</div>
                        <div class="text-4xl opacity-80 mb-4">Veuillez vous diriger vers le</div>
                        <div class="text-6xl font-bold bg-white text-gray-900 px-10 py-4 rounded-2xl shadow-inner inline-block">
                            {{ $lastCall->counter->name }}
                        </div>
                    </div>
                @else
                    <div class="text-center opacity-40">
                        <div class="text-4xl mb-4">Bienvenue</div>
                        <div class="text-2xl">Aucun ticket en cours d'appel</div>
                    </div>
                @endif
            </div>

            <!-- Bandeau d'information défilant -->
            <div class="h-20 rounded-2xl flex items-center overflow-hidden border-2 border-gray-700" style="background-color: var(--secondary-color)">
                <div class="w-32 h-full flex items-center justify-center font-bold text-lg px-4 z-10 shadow-lg" style="background-color: var(--primary-color)">
                    INFO
                </div>
                <div class="flex-grow whitespace-nowrap overflow-hidden">
                    <div class="inline-block animate-marquee text-2xl font-medium px-10">
                        {{ $settings['announcement'] }} • Merci de respecter l'ordre des tickets • {{ $settings['name'] }} à votre service
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar (File d'attente) -->
        <div class="{{ $layout === 'compact_bottom' ? 'h-1/3 flex flex-row' : 'w-1/4' }} flex flex-col gap-4">
            <div class="flex-grow rounded-3xl p-4 border-2 border-gray-700 flex flex-col overflow-hidden" style="background-color: var(--secondary-color)">
                <h2 class="text-xl font-bold mb-4 pb-2 border-b border-gray-700 flex items-center gap-2">
                    <i class="fas fa-history opacity-50"></i>
                    Appels Récents
                </h2>
                <div class="flex-grow space-y-3 overflow-y-auto pr-1 custom-scrollbar" id="recent-calls-list">
                    @foreach($recentCalls->skip(1) as $call)
                    <div class="flex justify-between items-center p-3 rounded-xl border-l-4" style="background-color: rgba(255,255,255,0.05); border-color: var(--primary-color)">
                        <div>
                            <div class="text-2xl font-bold">{{ $call->ticket_number }}</div>
                            <div class="text-xs opacity-50 uppercase">{{ $call->service->name }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold opacity-80">{{ $call->counter->code }}</div>
                            <div class="text-[10px] opacity-40 uppercase">Guichet</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Stats Rapides -->
            <div class="{{ $layout === 'compact_bottom' ? 'w-64' : 'h-32' }} rounded-3xl p-4 border-2 border-gray-700 grid grid-cols-2 gap-2" style="background-color: var(--secondary-color)">
                <div class="rounded-xl p-2 flex flex-col items-center justify-center" style="background-color: rgba(255,255,255,0.05)">
                    <div class="text-2xl font-bold text-blue-400">{{ $stats['waiting'] }}</div>
                    <div class="text-[10px] opacity-40 uppercase">Attente</div>
                </div>
                <div class="rounded-xl p-2 flex flex-col items-center justify-center" style="background-color: rgba(255,255,255,0.05)">
                    <div class="text-2xl font-bold text-green-400">{{ $stats['served_today'] }}</div>
                    <div class="text-[10px] opacity-40 uppercase">Servis</div>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    /* Masquer la navbar globale de VitalBridge sur l'écran public */
    nav { display: none !important; }
    
    .qmatic-display-root {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
    }

    @keyframes marquee {
        0% { transform: translateX(100%); }
        100% { transform: translateX(-100%); }
    }
    .animate-marquee {
        animation: marquee 25s linear infinite;
    }
    .animate-pulse-slow {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.9; transform: scale(1.02); }
    }

    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #1f2937; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 10px; }
</style>

<script>
    function toggleFullScreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }

    function updateTime() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    setInterval(updateTime, 1000);
    updateTime();

    function announceTicket(ticketNumber, counterName) {
        if (!('speechSynthesis' in window)) return;

        const gender = "{{ $settings['announcement_gender'] }}";
        const isMultiLang = {{ $settings['announcement_multi_lang'] ? 'true' : 'false' }};
        
        let messages = [];
        
        // 1. Français (x2)
        let mainTemplate = "{{ $settings['announcement_template'] }}";
        let mainText = mainTemplate.replace('{ticket}', ticketNumber).replace('{counter}', counterName);
        messages.push({ text: mainText, lang: "{{ $settings['announcement_language'] }}" });
        messages.push({ text: mainText, lang: "{{ $settings['announcement_language'] }}" });

        // 2. Langues Locales (x2 chaque)
        if (isMultiLang) {
            // On pourrait boucler sur local_languages ici si on les passait en JSON
            let mooreText = "{{ $settings['template_moore'] }}".replace('{ticket}', ticketNumber).replace('{counter}', counterName);
            messages.push({ text: mooreText, lang: "fr-FR" });
            messages.push({ text: mooreText, lang: "fr-FR" });

            let dioulaText = "{{ $settings['template_dioula'] }}".replace('{ticket}', ticketNumber).replace('{counter}', counterName);
            messages.push({ text: dioulaText, lang: "fr-FR" });
            messages.push({ text: dioulaText, lang: "fr-FR" });
        }

        function speakSequentially(index) {
            if (index >= messages.length) {
                setTimeout(() => window.location.reload(), 2000);
                return;
            }

            const utterance = new SpeechSynthesisUtterance(messages[index].text);
            utterance.lang = messages[index].lang;
            utterance.rate = 0.85;
            utterance.pitch = 1;

            const voices = window.speechSynthesis.getVoices();
            if (voices.length > 0) {
                const selectedVoice = voices.find(v => {
                    const name = v.name.toLowerCase();
                    const isGenderMatch = gender === 'male' ? 
                        (name.includes('male') || name.includes('guy') || name.includes('thomas') || name.includes('paul') || name.includes('natural')) : 
                        (name.includes('female') || name.includes('girl') || name.includes('marie') || name.includes('hortense') || name.includes('natural'));
                    const isPremium = name.includes('google') || name.includes('microsoft') || name.includes('premium') || name.includes('natural');
                    return v.lang.includes(utterance.lang.split('-')[0]) && isGenderMatch && isPremium;
                }) || voices.find(v => {
                    const name = v.name.toLowerCase();
                    const isGenderMatch = gender === 'male' ? (name.includes('male') || name.includes('guy')) : (name.includes('female') || name.includes('girl'));
                    return v.lang.includes(utterance.lang.split('-')[0]) && isGenderMatch;
                });
                if (selectedVoice) utterance.voice = selectedVoice;
            }

            utterance.onend = () => {
                setTimeout(() => speakSequentially(index + 1), 800);
            };

            window.speechSynthesis.speak(utterance);
        }

        if (window.speechSynthesis.getVoices().length === 0) {
            window.speechSynthesis.onvoiceschanged = () => speakSequentially(0);
        } else {
            speakSequentially(0);
        }
    }

    let lastTimestamp = '{{ now()->toIso8601String() }}';
    function checkUpdates() {
        fetch(`{{ route('qmatic.display.updates') }}?health_center_id={{ $healthCenterId }}&since=${lastTimestamp}`)
            .then(response => response.json())
            .then(data => {
                if (data.calls && data.calls.length > 0) {
                    const lastCall = data.calls[0];
                    const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
                    audio.play().catch(e => console.log("Audio play blocked"));
                    setTimeout(() => announceTicket(lastCall.ticket_number, lastCall.counter.name), 1000);
                }
                lastTimestamp = data.timestamp;
            });
    }
    setInterval(checkUpdates, 5000);
</script>
@endsection
