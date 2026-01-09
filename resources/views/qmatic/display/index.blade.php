@extends('layouts.qmatic')

@section('title', $settings['name'] . ' - Écran Public')

@section('content')
@php
    $layout = $settings['display_layout'] ?? 'sidebar_right';
    $bgColor = $settings['display_bg_color'] ?? '#f8fafc';
    $secondaryColor = $settings['display_secondary_color'] ?? '#ffffff';
    $textColor = $settings['display_text_color'] ?? '#1e293b';
    $primaryColor = $settings['color'] ?? '#2563eb';
@endphp

<div class="min-h-screen flex flex-col qmatic-display-root" style="background-color: {{ $bgColor }}; color: {{ $textColor }}; --primary-color: {{ $primaryColor }}; --secondary-color: {{ $secondaryColor }}">
    
    <!-- Header -->
    <header class="px-6 py-4 flex justify-between items-center border-b-4 shadow-sm" style="background-color: {{ $secondaryColor }}; border-color: {{ $primaryColor }}">
        <div class="flex items-center gap-4">
            @if($settings['logo'])
                <img src="{{ $settings['logo'] }}" alt="Logo" class="h-16 w-auto object-contain">
            @endif
            <div>
                <h1 class="text-2xl font-bold">{{ $settings['name'] }}</h1>
            </div>
        </div>
        <div class="text-right">
            <div class="text-sm opacity-60" id="current-date">{{ now()->translatedFormat('l d F Y') }}</div>
            <div class="text-4xl font-bold font-mono" id="current-time"></div>
            <button onclick="toggleFullScreen()" class="mt-2 px-3 py-1 rounded text-sm opacity-40 hover:opacity-100 transition-opacity">
                <i class="fas fa-expand"></i>
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex p-4 gap-4 overflow-hidden">
        
        <!-- Left Sidebar: Appels Récents -->
        <aside class="w-64 flex flex-col gap-4">
            <div class="rounded-2xl border-2 flex flex-col overflow-hidden shadow-lg" style="background-color: {{ $secondaryColor }}; border-color: {{ $primaryColor }}">
                <div class="px-4 py-3 font-bold text-lg flex items-center gap-2" style="background-color: {{ $primaryColor }}; color: white;">
                    <i class="fas fa-history"></i>
                    Appels Récents
                </div>
                <div class="flex-grow overflow-y-auto p-3 space-y-2 custom-scrollbar" id="recent-calls-list" style="max-height: calc(100vh - 250px);">
                    @foreach($recentCalls->skip(1)->take(8) as $call)
                    <div class="p-3 rounded-lg border-l-4 hover:shadow-md transition-shadow" style="background-color: {{ $bgColor }}; border-color: {{ $call->service->color ?? $primaryColor }}">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="text-xl font-bold">{{ $call->ticket_number }}</div>
                                <div class="text-xs opacity-60 uppercase truncate">{{ $call->service->name }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold" style="color: {{ $primaryColor }}">{{ $call->counter->code }}</div>
                                <div class="text-[9px] opacity-50 uppercase">Guichet</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Stats globales -->
            <div class="rounded-2xl border-2 p-4 shadow-lg" style="background-color: {{ $secondaryColor }}; border-color: {{ $primaryColor }}">
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="p-3 rounded-lg" style="background-color: {{ $bgColor }}">
                        <div class="text-3xl font-bold text-blue-600">{{ $stats['waiting'] }}</div>
                        <div class="text-xs opacity-60 uppercase mt-1">Attente</div>
                    </div>
                    <div class="p-3 rounded-lg" style="background-color: {{ $bgColor }}">
                        <div class="text-3xl font-bold text-green-600">{{ $stats['served_today'] }}</div>
                        <div class="text-xs opacity-60 uppercase mt-1">Servis</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Display Area -->
        <div class="flex-grow flex flex-col gap-4">
            
            <!-- Current Call Display -->
            <div class="flex-grow rounded-3xl border-4 flex items-center justify-center shadow-2xl relative" style="background-color: {{ $secondaryColor }}; border-color: {{ $primaryColor }}">
                @if($recentCalls->count() > 0)
                    @php $lastCall = $recentCalls->first(); @endphp
                    <div class="w-full h-full flex items-center justify-center py-4 px-6" id="main-call-container">
                        
                        <div class="flex flex-col items-center justify-center w-full space-y-4">
                            
                            <!-- Ticket Number - RESPONSIVE -->
                            <div class="text-center">
                                <div class="text-lg font-semibold opacity-50 uppercase tracking-widest mb-2">
                                    Ticket
                                </div>
                                <div class="ticket-number font-black" 
                                     style="color: {{ $primaryColor }}; font-size: clamp(6rem, 12vw, 10rem); line-height: 0.9;">
                                    {{ $lastCall->ticket_number }}
                                </div>
                            </div>
                            
                            <!-- Service Name -->
                            <div class="text-xl font-semibold opacity-70 text-center px-4">
                                {{ $lastCall->service->name }}
                            </div>
                            
                            <!-- Divider -->
                            <div class="w-32 h-1 rounded-full opacity-30" style="background-color: {{ $primaryColor }}"></div>
                            
                            <!-- Direction Message -->
                            <div class="text-2xl font-semibold opacity-60 text-center">
                                Veuillez vous présenter au
                            </div>
                            
                            <!-- Guichet Display -->
                            <div class="flex flex-col items-center gap-2">
                                <div class="text-base font-bold uppercase tracking-wider opacity-50">
                                    Guichet
                                </div>
                                <div class="px-16 py-6 rounded-3xl shadow-2xl" 
                                     style="background: {{ $primaryColor }}; color: white;">
                                    <div class="font-black text-center" style="font-size: clamp(4rem, 8vw, 6rem); line-height: 1;">
                                        {{ $lastCall->counter->name }}
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                @else
                    <div class="text-center opacity-30 p-8">
                        <div class="mb-4">
                            <i class="fas fa-ticket-alt" style="font-size: 6rem;"></i>
                        </div>
                        <div class="text-3xl font-bold mb-2">En attente</div>
                        <div class="text-xl opacity-70">Aucun ticket appelé</div>
                    </div>
                @endif
            </div>

            <!-- Info Banner -->
            <div class="h-14 rounded-2xl flex items-center overflow-hidden border-2 shadow-lg" style="background-color: {{ $secondaryColor }}; border-color: {{ $primaryColor }}">
                <div class="w-20 h-full flex items-center justify-center font-bold text-sm shadow-lg" style="background-color: {{ $primaryColor }}; color: white;">
                    INFO
                </div>
                <div class="flex-grow overflow-hidden">
                    <div class="inline-block animate-marquee text-lg font-medium px-6 whitespace-nowrap">
                        {{ $settings['announcement'] }} • Merci de respecter l'ordre des tickets • {{ $settings['name'] }} à votre service
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Sidebar: Service Queue Statistics -->
        @if(count($serviceStats) > 0)
        <aside class="w-72 flex flex-col">
            <div class="rounded-2xl border-2 flex flex-col overflow-hidden shadow-lg h-full" style="background-color: {{ $secondaryColor }}; border-color: {{ $primaryColor }}">
                <div class="px-4 py-3 font-bold text-lg flex items-center gap-2" style="background-color: {{ $primaryColor }}; color: white;">
                    <i class="fas fa-users"></i>
                    File par Service
                </div>
                <div class="flex-grow overflow-y-auto p-3 space-y-2 custom-scrollbar" style="max-height: calc(100vh - 200px);">
                    @foreach($serviceStats as $serviceId => $stat)
                    <div class="p-4 rounded-xl border-l-4 hover:shadow-md transition-all" 
                         style="background-color: {{ $bgColor }}; border-color: {{ $stat['color'] ?? $primaryColor }}"
                         data-service-id="{{ $serviceId }}">
                        <div class="text-sm font-semibold mb-2 truncate" title="{{ $stat['name'] }}">
                            {{ $stat['name'] }}
                        </div>
                        <div class="flex items-end justify-between">
                            <div>
                                <div class="text-4xl font-black" data-waiting-count style="color: {{ $stat['color'] ?? $primaryColor }}">
                                    {{ $stat['waiting'] }}
                                </div>
                            </div>
                            <div class="text-[10px] opacity-50 uppercase pb-1">
                                En attente
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </aside>
        @endif
    </main>
</div>

<style>
    /* Masquer la navbar globale */
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
        animation: marquee 30s linear infinite;
    }
    
    @keyframes pulse {
        0%, 100% { 
            opacity: 1; 
            transform: scale(1); 
        }
        50% { 
            opacity: 0.95; 
            transform: scale(1.03); 
        }
    }
    
    @keyframes glow {
        0%, 100% { 
            filter: drop-shadow(0 0 10px currentColor); 
        }
        50% { 
            filter: drop-shadow(0 0 30px currentColor); 
        }
    }

    .ticket-number {
        animation: pulse 3s ease-in-out infinite, glow 2s ease-in-out infinite;
    }

    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    [data-waiting-count] {
        transition: transform 0.3s ease, color 0.3s ease;
    }
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
        
        let mainTemplate = "{{ $settings['announcement_template'] }}";
        let mainText = mainTemplate.replace('{ticket}', ticketNumber).replace('{counter}', counterName);
        messages.push({ text: mainText, lang: "{{ $settings['announcement_language'] }}" });
        messages.push({ text: mainText, lang: "{{ $settings['announcement_language'] }}" });

        if (isMultiLang) {
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
                
                if (data.serviceStats) {
                    Object.keys(data.serviceStats).forEach(serviceId => {
                        const stat = data.serviceStats[serviceId];
                        const serviceElement = document.querySelector(`[data-service-id="${serviceId}"]`);
                        if (serviceElement) {
                            const countElement = serviceElement.querySelector('[data-waiting-count]');
                            if (countElement) {
                                const newCount = stat.waiting;
                                const oldCount = parseInt(countElement.textContent) || 0;
                                
                                if (newCount !== oldCount) {
                                    countElement.textContent = newCount;
                                    countElement.style.transform = 'scale(1.2)';
                                    countElement.style.color = newCount > oldCount ? '#ef4444' : '#22c55e';
                                    setTimeout(() => {
                                        countElement.style.transform = 'scale(1)';
                                        countElement.style.color = '';
                                    }, 300);
                                }
                            }
                        }
                    });
                }
                
                lastTimestamp = data.timestamp;
            });
    }
    setInterval(checkUpdates, 5000);
</script>
@endsection
