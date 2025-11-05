@extends('layouts.app-dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gestion des horaires</h1>
        <p class="text-gray-600">Définissez les horaires de consultation pour le Dr {{ $doctor->first_name }} {{ $doctor->last_name }}</p>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
        <form action="{{ route('doctors.schedule.update', $doctor) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="border-t border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jour</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Disponible</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure d'ouverture</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure de fermeture</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $days = [
                                'monday' => 'Lundi',
                                'tuesday' => 'Mardi',
                                'wednesday' => 'Mercredi',
                                'thursday' => 'Jeudi',
                                'friday' => 'Vendredi',
                                'saturday' => 'Samedi',
                                'sunday' => 'Dimanche'
                            ];
                            
                            $schedules = $schedules->keyBy('day_of_week');
                        @endphp
                        
                        @foreach($days as $key => $day)
                            @php 
                                $schedule = $schedules->get($key);
                                $startTime = $schedule ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '09:00';
                                $endTime = $schedule ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '17:00';
                                $isAvailable = $schedule ? $schedule->is_available : false;
                            @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $day }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="{{ $key }}_available" id="{{ $key }}_available" 
                                        {{ $isAvailable ? 'checked' : '' }}
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="time" name="{{ $key }}_start" id="{{ $key }}_start" 
                                        value="{{ $startTime }}"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="time" name="{{ $key }}_end" id="{{ $key }}_end" 
                                        value="{{ $endTime }}"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-4 bg-gray-50 text-right sm:px-6">
                <a href="{{ route('doctors.show', $doctor) }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Annuler
                </a>
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    <!-- Formulaire d'indisponibilité -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-8">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Déclarer une indisponibilité</h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('doctors.unavailable', $doctor) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <label for="unavailable_date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" name="unavailable_date" id="unavailable_date" 
                            min="{{ now()->format('Y-m-d') }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700">De</label>
                        <input type="time" name="start_time" id="start_time" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700">À</label>
                        <input type="time" name="end_time" id="end_time" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-red-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            Enregistrer l'indisponibilité
                        </button>
                    </div>
                </div>
                <div class="mt-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700">Raison</label>
                    <input type="text" name="reason" id="reason" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Ex: Formation, Congé, etc.">
                </div>
            </form>
        </div>
    </div>

    <!-- Formulaire de retard -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Déclarer un retard</h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <form action="{{ route('doctors.delay', $doctor) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="delay_start" class="block text-sm font-medium text-gray-700">Date et heure du retard</label>
                        <input type="datetime-local" name="delay_start" id="delay_start" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="delay_duration" class="block text-sm font-medium text-gray-700">Durée (en minutes)</label>
                        <input type="number" name="delay_duration" id="delay_duration" min="1" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-yellow-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                            Enregistrer le retard
                        </button>
                    </div>
                </div>
                <div class="mt-4">
                    <label for="delay_reason" class="block text-sm font-medium text-gray-700">Raison</label>
                    <input type="text" name="reason" id="delay_reason" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        placeholder="Ex: Embouteillage, Problème de transport, etc.">
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialiser les valeurs par défaut pour la date et l'heure actuelles
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        const timeString = now.toTimeString().slice(0, 5); // Format HH:MM
        const dateString = now.toISOString().slice(0, 16); // Format YYYY-MM-DDTHH:MM
        
        // Définir l'heure actuelle + 5 minutes comme heure de début par défaut
        const startTime = document.getElementById('start_time');
        if (startTime && !startTime.value) {
            const start = new Date(now.getTime() + 5 * 60000); // +5 minutes
            startTime.value = start.toTimeString().slice(0, 5);
        }
        
        // Définir l'heure actuelle + 1h comme heure de fin par défaut
        const endTime = document.getElementById('end_time');
        if (endTime && !endTime.value) {
            const end = new Date(now.getTime() + 60 * 60000); // +1 heure
            endTime.value = end.toTimeString().slice(0, 5);
        }
        
        // Définir la date et l'heure actuelles pour le retard
        const delayStart = document.getElementById('delay_start');
        if (delayStart && !delayStart.value) {
            delayStart.value = dateString;
        }
        
        // Définir une durée de retard par défaut de 15 minutes
        const delayDuration = document.getElementById('delay_duration');
        if (delayDuration && !delayDuration.value) {
            delayDuration.value = '15';
        }
    });
    
    // Validation des heures de début/fin
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            // Validation des heures de début/fin pour les horaires
            if (this.id === 'schedule-form') {
                const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                let isValid = true;
                
                days.forEach(day => {
                    const start = document.getElementById(`${day}_start`);
                    const end = document.getElementById(`${day}_end`);
                    const available = document.getElementById(`${day}_available`);
                    
                    if (available && available.checked && start && end) {
                        if (start.value >= end.value) {
                            alert(`Pour le ${day}, l'heure de fin doit être postérieure à l'heure de début.`);
                            isValid = false;
                            return false;
                        }
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    return false;
                }
            }
            
            // Validation des heures de début/fin pour les indisponibilités
            if (this.action.includes('unavailable')) {
                const start = document.getElementById('start_time');
                const end = document.getElementById('end_time');
                
                if (start && end && start.value >= end.value) {
                    alert("L'heure de fin doit être postérieure à l'heure de début.");
                    e.preventDefault();
                    return false;
                }
            }
        });
    });
</script>
@endpush
@endsection
