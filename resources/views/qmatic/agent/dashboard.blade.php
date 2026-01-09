@extends('layouts.qmatic')

@section('title', 'Interface Agent - Qmatic')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-140px)]">
    <!-- Colonne Gauche: Contrôles et Ticket Actuel -->
    <div class="lg:col-span-2 flex flex-col gap-6">
        <!-- Info Guichet -->
        <div class="qmatic-card p-4 flex justify-between items-center bg-gray-800 text-white">
            <div>
                <span class="text-gray-400 text-sm uppercase tracking-wider">Guichet</span>
                <div class="text-2xl font-bold">{{ $counter->code }} - {{ $counter->name }}</div>
            </div>
            <form action="{{ route('qmatic.agent.counter.release') }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment quitter ce guichet ?');">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm font-medium transition-colors">
                    Quitter le guichet
                </button>
            </form>
        </div>

        <!-- Zone Principale -->
        <div class="qmatic-card flex-1 flex flex-col justify-center items-center p-8 text-center relative overflow-hidden">
            @if($currentTicket)
                <div class="absolute top-4 right-4">
                    <span class="status-badge status-{{ $currentTicket->status }} text-lg px-4 py-1">
                        {{ ucfirst($currentTicket->status) }}
                    </span>
                </div>

                <div class="mb-2 text-gray-500 uppercase tracking-widest text-sm">Ticket en cours</div>
                <div class="text-8xl font-black text-gray-900 mb-4 tracking-wider">{{ $currentTicket->ticket_number }}</div>
                <div class="text-xl text-blue-600 font-medium mb-8">{{ $currentTicket->service->name }}</div>

                <div class="grid grid-cols-2 gap-4 w-full max-w-md">
                    @if($currentTicket->status === 'called')
                        <form action="{{ route('qmatic.agent.start-serving') }}" method="POST" class="col-span-2">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-lg text-lg font-bold shadow-lg transition-transform transform hover:scale-105">
                                Démarrer le service
                            </button>
                        </form>
                        
                        <form action="{{ route('qmatic.agent.recall') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white py-3 rounded-lg font-medium">
                                Rappeler
                            </button>
                        </form>
                        
                        <form action="{{ route('qmatic.agent.mark-absent') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-3 rounded-lg font-medium">
                                Absent
                            </button>
                        </form>
                    @elseif($currentTicket->status === 'serving')
                        <form action="{{ route('qmatic.agent.mark-served') }}" method="POST" class="col-span-2">
                            @csrf
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-lg text-lg font-bold shadow-lg transition-transform transform hover:scale-105">
                                Terminer (Servi)
                            </button>
                        </form>
                        
                        <form action="{{ route('qmatic.agent.requeue') }}" method="POST" class="col-span-2">
                            @csrf
                            <button type="submit" class="w-full bg-gray-500 hover:bg-gray-600 text-white py-2 rounded-lg text-sm">
                                Remettre en file d'attente
                            </button>
                        </form>
                    @endif
                </div>
                
                <div class="mt-8 text-sm text-gray-400">
                    Arrivé à: {{ $currentTicket->created_at->format('H:i') }} • 
                    Attente: {{ $currentTicket->wait_time }} min
                </div>
            @else
                <div class="text-gray-400 mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mx-auto mb-4 opacity-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-xl">En attente du prochain client</p>
                </div>
                
                <form action="{{ route('qmatic.agent.call-next') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-full text-xl font-bold shadow-lg transition-transform transform hover:scale-105 flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Appeler le suivant
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Colonne Droite: File d'attente et Stats -->
    <div class="flex flex-col gap-6">
        <!-- Stats -->
        <div class="qmatic-card p-4">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Performance du jour</h3>
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="bg-green-50 p-2 rounded">
                    <div class="text-xl font-bold text-green-700">{{ $todayStats['served'] }}</div>
                    <div class="text-xs text-green-600">Servis</div>
                </div>
                <div class="bg-red-50 p-2 rounded">
                    <div class="text-xl font-bold text-red-700">{{ $todayStats['absent'] }}</div>
                    <div class="text-xs text-red-600">Absents</div>
                </div>
                <div class="bg-blue-50 p-2 rounded">
                    <div class="text-xl font-bold text-blue-700">{{ round($todayStats['avg_service_time'] ?? 0) }}m</div>
                    <div class="text-xs text-blue-600">Moyenne</div>
                </div>
            </div>
        </div>

        <!-- File d'attente -->
        <div class="qmatic-card flex-1 flex flex-col overflow-hidden">
            <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-700">File d'attente</h3>
                <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded-full">{{ $waitingTickets->count() }}</span>
            </div>
            
            <div class="overflow-y-auto flex-1 p-0">
                @if($waitingTickets->count() > 0)
                    <ul class="divide-y divide-gray-100">
                        @foreach($waitingTickets as $ticket)
                        <li class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-lg font-bold text-gray-900">{{ $ticket->ticket_number }}</span>
                                    <div class="text-xs text-gray-500">{{ $ticket->service->name }}</div>
                                </div>
                                <div class="text-right">
                                    @if($ticket->priority !== 'normal')
                                        <span class="text-xs font-bold uppercase priority-{{ $ticket->priority }} block mb-1">
                                            {{ $ticket->priority }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ $ticket->created_at->format('H:i') }}</span>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="p-8 text-center text-gray-400">
                        <p>Aucun ticket en attente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
