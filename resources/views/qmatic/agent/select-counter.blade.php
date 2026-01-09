@extends('layouts.qmatic')

@section('title', 'Sélection du Guichet - Qmatic')

@section('content')
<div class="max-w-2xl mx-auto py-10">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-gray-900">Sélectionnez votre guichet</h1>
        <p class="text-gray-600 mt-2">Choisissez un guichet disponible pour commencer à travailler</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        @forelse($availableCounters as $counter)
        <form action="{{ route('qmatic.agent.counter.assign') }}" method="POST">
            @csrf
            <input type="hidden" name="counter_id" value="{{ $counter->id }}">
            
            <button type="submit" class="w-full qmatic-card p-6 hover:shadow-lg transition-all duration-300 text-left border-l-4 border-blue-500 hover:bg-blue-50">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-2xl font-bold text-gray-900">{{ $counter->code }}</span>
                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Disponible</span>
                </div>
                <h3 class="text-lg font-medium text-gray-800">{{ $counter->name }}</h3>
                <p class="text-sm text-gray-500 mt-2">
                    @if($counter->service_ids)
                        Services limités
                    @else
                        Tous les services
                    @endif
                </p>
            </button>
        </form>
        @empty
        <div class="col-span-2 text-center py-10 bg-white rounded-lg shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900">Aucun guichet disponible</h3>
            <p class="text-gray-500 mt-2">Tous les guichets sont occupés ou inactifs.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
