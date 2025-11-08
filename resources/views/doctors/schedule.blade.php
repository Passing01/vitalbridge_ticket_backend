@extends('layouts.app-dashboard')

@section('content')
<div class="w-full p-6 mx-auto">
    <div class="relative flex items-center p-0 overflow-hidden bg-center bg-cover min-h-75 rounded-2xl" style="background-image: url('{{ asset('assets/img/curved-images/curved0.jpg') }}');">
        <span class="absolute inset-y-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-blue-600 to-violet-600 opacity-60"></span>
    </div>
    
    <div class="relative flex flex-col flex-auto min-w-0 p-4 mx-6 -mt-16 overflow-hidden break-words border-0 shadow-blur rounded-2xl bg-white/80 bg-clip-border backdrop-blur-2xl backdrop-saturate-200">
        <div class="flex flex-wrap -mx-3">
            <div class="flex-none w-auto max-w-full px-3">
                <div class="text-base ease-soft-in-out h-18.5 w-18.5 relative inline-flex items-center justify-center rounded-xl text-white transition-all duration-200">
                    <img src="{{ $doctor->profile_photo_path ? asset('storage/' . $doctor->profile_photo_path) : asset('assets/img/team-2.jpg') }}" alt="{{ $doctor->name }}" class="w-full shadow-soft-sm rounded-xl" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 mx-auto mt-4 sm:my-auto sm:mr-0 md:w-1/2 md:flex-none lg:w-4/12">
                <div class="h-full">
                    <h5 class="mb-1">Gestion des horaires</h5>
                    <p class="mb-0 font-semibold leading-normal text-sm">Définissez les horaires de consultation pour le Dr {{ $doctor->first_name }} {{ $doctor->last_name }}</p>
                </div>
                <div class="flex items-center justify-end p-4 border-t border-solid border-slate-100 rounded-b">
                    <a href="{{ route('doctors.show', $doctor) }}" class="inline-block px-6 py-3 mb-0 font-bold text-center text-slate-700 uppercase align-middle transition-all bg-transparent border border-solid rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs border-slate-300 hover:bg-slate-100 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25">
                        Annuler
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="flex w-full p-4 mt-4 mb-4 text-sm text-white bg-green-500 rounded-lg" role="alert">
            <i class="fas fa-check-circle mr-3"></i>
            <span class="sr-only">Succès</span>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex w-full p-4 mt-4 mb-4 text-sm text-white bg-red-500 rounded-lg" role="alert">
            <i class="fas fa-exclamation-circle mr-3"></i>
            <span class="sr-only">Erreur</span>
            <div>
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <div class="w-full max-w-full px-3 mt-6">
        <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
            <form action="{{ route('doctors.schedule.update', $doctor) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                    <h6 class="mb-0">Horaires de consultation</h6>
                    <p class="text-sm text-slate-500">Définissez les horaires d'ouverture pour chaque jour de la semaine</p>
                </div>
                
                <div class="flex-auto p-4">
                    <div class="overflow-x-auto">
                        <table class="items-center w-full mb-4 align-top border-gray-200 text-slate-500">
                            <thead class="align-bottom">
                                <tr>
                                    <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Jour</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Disponible</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Heure d'ouverture</th>
                                    <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Heure de fermeture</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                        <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <div class="flex px-2 py-1">
                                                <div class="flex flex-col justify-center">
                                                    <h6 class="mb-0 text-sm leading-normal">{{ $day }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent text-center">
                                            <div class="inline-block w-6 h-6 text-center">
                                                <input type="checkbox" name="{{ $key }}_available" id="{{ $key }}_available" 
                                                    {{ $isAvailable ? 'checked' : '' }}
                                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            </div>
                                        </td>
                                        <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <input type="time" name="{{ $key }}_start" id="{{ $key }}_start" 
                                                value="{{ $startTime }}"
                                                class="text-sm leading-4.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-blue-500 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow">
                                        </td>
                                        <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                            <input type="time" name="{{ $key }}_end" id="{{ $key }}_end" 
                                                value="{{ $endTime }}"
                                                class="text-sm leading-4.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-blue-500 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex items-center justify-end p-4 border-t border-solid border-slate-100 rounded-b">
                    <a href="{{ route('doctors.show', $doctor) }}" class="inline-block px-6 py-3 mb-0 font-bold text-center text-slate-700 uppercase align-middle transition-all bg-transparent border border-solid rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs border-slate-300 hover:bg-slate-100 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25">
                        Annuler
                    </a>
                    <button type="submit" class="inline-block px-6 py-3 mb-0 ml-2 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-blue-600 to-violet-600 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25">
                        <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Formulaire d'indisponibilité -->
    <div class="w-full max-w-full px-3 mt-6">
        <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                <h6 class="mb-0">Déclarer une indisponibilité</h6>
                <p class="text-sm text-slate-500">Ajoutez des périodes d'indisponibilité exceptionnelles</p>
            </div>
            <div class="flex-auto p-4">
                <form action="{{ route('doctors.unavailable', $doctor) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="flex flex-col">
                            <label for="start_date" class="mb-2 ml-1 text-xs font-semibold text-slate-700">Date de début</label>
                            <input type="datetime-local" name="start_date" id="start_date" 
                                   class="text-sm leading-4.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-blue-500 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow"
                                   required>
                        </div>
                        <div class="flex flex-col">
                            <label for="end_date" class="mb-2 ml-1 text-xs font-semibold text-slate-700">Date de fin</label>
                            <input type="datetime-local" name="end_date" id="end_date" 
                                   class="text-sm leading-4.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-blue-500 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow"
                                   required>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="reason" class="mb-2 ml-1 text-xs font-semibold text-slate-700">Raison (optionnel)</label>
                            <input type="text" name="reason" id="reason" 
                                   class="text-sm leading-4.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-blue-500 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow">
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-blue-600 to-violet-600 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25">
                            <i class="fas fa-calendar-times mr-2"></i> Ajouter l'indisponibilité
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Section des retards -->
    <div class="w-full max-w-full px-3 mt-6">
        <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
            <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                <h6 class="mb-0">Déclarer un retard</h6>
                <p class="text-sm text-slate-500">Signalez un retard exceptionnel du médecin</p>
            </div>
            <div class="flex-auto p-4">
                <form action="{{ route('doctors.delay', $doctor) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="flex flex-col">
                            <label for="date" class="mb-2 ml-1 text-xs font-semibold text-slate-700">Date</label>
                            <input type="date" name="date" id="date" 
                                   class="text-sm leading-4.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-blue-500 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow"
                                   required>
                        </div>
                        <div class="flex flex-col">
                            <label for="delay_minutes" class="mb-2 ml-1 text-xs font-semibold text-slate-700">Durée du retard (minutes)</label>
                            <input type="number" name="delay_minutes" id="delay_minutes" min="1" 
                                   class="text-sm leading-4.6 ease-soft block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding py-2 px-3 font-normal text-gray-700 transition-all focus:border-blue-500 focus:bg-white focus:text-gray-700 focus:outline-none focus:transition-shadow"
                                   required>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="inline-block px-6 py-3 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-orange-500 to-yellow-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25">
                                <i class="fas fa-clock mr-2"></i> Enregistrer le retard
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialiser les valeurs par défaut pour la date et l'heure actuelles
    document.addEventListener('DOMContentLoaded', function() {
        // Définir la date et l'heure actuelles pour le formulaire d'indisponibilité
        const now = new Date();
        
        // Format YYYY-MM-DDTHH:MM pour les champs datetime-local
        const formatDateTimeLocal = (date) => {
            const pad = (num) => num.toString().padStart(2, '0');
            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
        };
        
        // Définir les valeurs par défaut pour le formulaire d'indisponibilité
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        if (startDateInput && !startDateInput.value) {
            startDateInput.value = formatDateTimeLocal(now);
        }
        
        if (endDateInput && !endDateInput.value) {
            const endDate = new Date(now);
            endDate.setHours(endDate.getHours() + 1); // Par défaut, 1 heure plus tard
            endDateInput.value = formatDateTimeLocal(endDate);
        }
        
        // Définir la date d'aujourd'hui par défaut pour le formulaire de retard
        const dateInput = document.getElementById('date');
        if (dateInput && !dateInput.value) {
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            dateInput.value = formattedDate;
        }

        // Activer/désactiver les champs d'heure en fonction de la case à cocher
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name$="_available"]');
        checkboxes.forEach(checkbox => {
            const day = checkbox.name.replace('_available', '');
            const startInput = document.getElementById(`${day}_start`);
            const endInput = document.getElementById(`${day}_end`);
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
