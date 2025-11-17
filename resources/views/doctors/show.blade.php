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
                    <img src="{{ $doctor->profile_photo_url }}" alt="{{ $doctor->full_name }}" class="w-full h-full object-cover shadow-soft-sm rounded-xl" />
                </div>
            </div>
            <div class="w-full max-w-full px-3 mx-auto mt-4 sm:my-auto sm:mr-0 md:w-1/2 md:flex-none lg:w-4/12">
                <div class="h-full">
                    <h5 class="mb-1">{{ $doctor->first_name }} {{ $doctor->last_name }}</h5>
                    <p class="mb-0 font-semibold leading-normal text-sm">
                        @if($doctor->doctorProfiles->count() > 0)
                            {{ $doctor->doctorProfiles->first()->qualification ?? 'Médecin' }}
                            @if($doctor->doctorProfiles->count() > 1)
                                <span class="text-xs text-gray-500">({{ $doctor->doctorProfiles->count() }} affiliations)</span>
                            @endif
                        @else
                            Médecin
                        @endif
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('doctors.edit', $doctor) }}" class="inline-block px-6 py-3 mb-0 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-blue-600 to-violet-600 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25">
                            <i class="fas fa-edit mr-2"></i>Modifier
                        </a>
                        <a href="{{ route('doctors.schedule', $doctor) }}" class="inline-block px-6 py-3 mb-0 ml-2 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-green-600 to-lime-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25">
                            <i class="fas fa-calendar-alt mr-2"></i>Horaires
                        </a>
                        <a href="{{ route('doctors.index') }}" class="inline-block px-6 py-3 mb-0 ml-2 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-red-600 to-pink-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25">
                            <i class="fas fa-calendar-alt mr-2"></i>Retour
                        </a>
                    </div>
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

    <div class="flex flex-wrap mt-6 -mx-3">
        <!-- Informations personnelles -->
        <div class="w-full max-w-full px-3 mb-6 md:mb-0 md:w-4/12 md:flex-none">
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                    <h6 class="mb-0">Informations personnelles</h6>
                </div>
                <div class="flex-auto p-4">
                    <ul class="flex flex-col pl-0 mb-0 rounded-lg">
                        <li class="relative flex p-6 mb-2 border-0 rounded-t-inherit rounded-xl bg-gray-50">
                            <div class="flex flex-col">
                                <h6 class="mb-4 leading-tight text-xs">Nom complet</h6>
                                <span class="font-semibold leading-tight text-sm">{{ $doctor->first_name }} {{ $doctor->last_name }}</span>
                            </div>
                        </li>
                        <li class="relative flex p-6 mb-2 border-0 rounded-xl">
                            <div class="flex flex-col">
                                <h6 class="mb-4 leading-tight text-xs">Email</h6>
                                <span class="font-semibold leading-tight text-sm">{{ $doctor->email }}</span>
                            </div>
                        </li>
                        <li class="relative flex p-6 border-0 rounded-b-xl">
                            <div class="flex flex-col">
                                <h6 class="mb-4 leading-tight text-xs">Téléphone</h6>
                                <span class="font-semibold leading-tight text-sm">{{ $doctor->phone ?? 'Non spécifié' }}</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Affiliations professionnelles -->
        <div class="w-full max-w-full px-3 mb-6 md:mb-0 md:w-4/12 md:flex-none">
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h6 class="mb-0">Affiliations ({{ $doctor->doctorProfiles->count() }})</h6>
                        <a href="{{ route('doctors.edit', $doctor) }}" class="text-xs text-blue-600 hover:text-blue-800">
                            <i class="fas fa-plus mr-1"></i> Ajouter
                        </a>
                    </div>
                </div>
                <div class="flex-auto p-4">
                    @if($doctor->doctorProfiles->count() > 0)
                        <div class="space-y-3">
                            @foreach($doctor->doctorProfiles as $profile)
                                <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h6 class="text-xs font-semibold text-gray-900 mb-1">
                                                {{ $profile->specialty->name ?? 'Non spécifié' }}
                                            </h6>
                                            <p class="text-xs text-gray-600 mb-1">
                                                {{ $profile->specialty->department->name ?? 'Non spécifié' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $profile->qualification }}
                                            </p>
                                            @if($profile->is_available)
                                                <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    Disponible
                                                </span>
                                            @else
                                                <span class="inline-block mt-1 px-2 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                    Indisponible
                                                </span>
                                            @endif
                                        </div>
                                        <a href="{{ route('doctors.edit', $doctor) }}?profile={{ $profile->id }}" 
                                           class="ml-2 text-blue-600 hover:text-blue-800 text-xs">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-500 text-center py-4">Aucune affiliation</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Détails supplémentaires -->
        <div class="w-full max-w-full px-3 md:w-4/12 md:flex-none">
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                    <h6 class="mb-0">Détails supplémentaires</h6>
                </div>
                <div class="flex-auto p-4">
                    <ul class="flex flex-col pl-0 mb-0 rounded-lg">
                        <li class="relative flex p-6 mb-2 border-0 rounded-t-inherit rounded-xl bg-gray-50">
                            <div class="flex flex-col">
                                <h6 class="mb-4 leading-tight text-xs">Patients par jour</h6>
                                <span class="font-semibold leading-tight text-sm">
                                    @if($doctor->doctorProfiles->count() > 0)
                                        {{ $doctor->doctorProfiles->first()->max_patients_per_day ?? 20 }} patients
                                    @else
                                        20 patients
                                    @endif
                                </span>
                            </div>
                        </li>
                        <li class="relative flex p-6 mb-2 border-0 rounded-xl">
                            <div class="flex flex-col">
                                <h6 class="mb-4 leading-tight text-xs">Durée de consultation</h6>
                                <span class="font-semibold leading-tight text-sm">
                                    @if($doctor->doctorProfiles->count() > 0)
                                        {{ $doctor->doctorProfiles->first()->average_consultation_time ?? 30 }} minutes
                                    @else
                                        30 minutes
                                    @endif
                                </span>
                            </div>
                        </li>
                        <li class="relative flex p-6 border-0 rounded-b-xl">
                            <div class="flex flex-col">
                                <h6 class="mb-4 leading-tight text-xs">Dernière connexion</h6>
                                <span class="font-semibold leading-tight text-sm">
                                    {{ $doctor->last_login_at ? $doctor->last_login_at->diffForHumans() : 'Jamais connecté' }}
                                </span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
            <div class="px-4 py-5 sm:px-6 bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Biographie</h3>
            </div>
            <div class="px-4 py-5 sm:p-6">
                @if($doctor->doctorProfiles->count() > 0 && $doctor->doctorProfiles->first()->bio)
                    <p class="text-sm text-gray-700">{{ $doctor->doctorProfiles->first()->bio }}</p>
                @else
                    <p class="text-sm text-gray-500 italic">Aucune biographie disponible</p>
                @endif
            </div>
        </div>
    
    <!-- Section des horaires par affiliation -->
    @if($doctor->doctorProfiles->count() > 0)
        @foreach($doctor->doctorProfiles as $profile)
            <div class="w-full max-w-full px-3 mt-6">
                <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                    <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                        <div class="flex justify-between items-center">
                            <h6 class="mb-0">Horaires - {{ $profile->specialty->name ?? 'Non spécifié' }} ({{ $profile->specialty->department->name ?? 'Non spécifié' }})</h6>
                            <a href="{{ route('doctors.schedule', $doctor) }}" class="inline-block px-3 py-1.5 text-xs font-bold text-center text-white uppercase align-middle transition-all bg-transparent border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-blue-600 to-violet-600 leading-pro">
                                <i class="fas fa-edit mr-1"></i> Modifier
                            </a>
                        </div>
                    </div>
                    <div class="flex-auto p-4">
                        <div class="overflow-x-auto">
                            <table class="items-center w-full mb-4 align-top border-gray-200 text-slate-500">
                                <thead class="align-bottom">
                                    <tr>
                                        <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Jour</th>
                                        <th class="px-6 py-3 pl-2 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Statut</th>
                                        <th class="px-6 py-3 font-bold text-center uppercase align-middle bg-transparent border-b border-gray-200 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 opacity-70">Heures d'ouverture</th>
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
                                        
                                        $schedules = $profile->schedules->keyBy('day_of_week');
                                    @endphp
                                    
                                    @foreach($days as $key => $day)
                                        @php 
                                            $schedule = $schedules->get($key);
                                            $startTime = $schedule ? \Carbon\Carbon::parse($schedule->start_time)->format('H:i') : '-';
                                            $endTime = $schedule ? \Carbon\Carbon::parse($schedule->end_time)->format('H:i') : '-';
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
                                            <td class="p-2 align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                @if($isAvailable)
                                                    <span class="px-2.5 py-1.5 text-xs font-bold inline-flex items-center justify-center text-center align-middle transition-all bg-transparent border border-solid rounded-lg cursor-pointer bg-green-100 border-green-200 text-green-600">
                                                        <i class="fas fa-check-circle mr-1"></i> Ouvert
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1.5 text-xs font-bold inline-flex items-center justify-center text-center align-middle transition-all bg-transparent border border-solid rounded-lg cursor-pointer bg-red-100 border-red-200 text-red-600">
                                                        <i class="fas fa-times-circle mr-1"></i> Fermé
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="p-2 text-center align-middle bg-transparent border-b whitespace-nowrap shadow-transparent">
                                                @if($isAvailable)
                                                    <span class="text-xs font-semibold leading-tight text-slate-700">{{ $startTime }} - {{ $endTime }}</span>
                                                @else
                                                    <span class="text-xs font-semibold leading-tight text-slate-400">Fermé</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Section des indisponibilités à venir -->
    @php
        $allUnavailabilities = collect();
        foreach($doctor->doctorProfiles as $profile) {
            $allUnavailabilities = $allUnavailabilities->merge($profile->unavailabilities->where('unavailable_date', '>=', now()->toDateString()));
        }
        $allUnavailabilities = $allUnavailabilities->sortBy('unavailable_date');
    @endphp
    @if($allUnavailabilities->isNotEmpty())
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Indisponibilités à venir</h3>
        </div>
        <div class="border-t border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Affiliation</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">De</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">À</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Raison</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($allUnavailabilities as $unavailability)
                        @php
                            $profile = $doctor->doctorProfiles->firstWhere('id', $unavailability->doctor_profile_id);
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $profile->specialty->name ?? 'Non spécifié' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($unavailability->unavailable_date)->isoFormat('dddd D MMMM YYYY') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($unavailability->start_time)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($unavailability->end_time)->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $unavailability->reason }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Section des retards récents -->
    @php
        $allDelays = collect();
        foreach($doctor->doctorProfiles as $profile) {
            $allDelays = $allDelays->merge($profile->delays->where('is_active', true));
        }
        $allDelays = $allDelays->sortByDesc('delay_start')->take(5);
    @endphp
    @if($allDelays->isNotEmpty())
    <div class="mt-8 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Retards récents</h3>
        </div>
        <div class="border-t border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Affiliation</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date et heure</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Durée</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Raison</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($allDelays as $delay)
                        @php
                            $profile = $doctor->doctorProfiles->firstWhere('id', $delay->doctor_profile_id);
                        @endphp
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $profile->specialty->name ?? 'Non spécifié' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $delay->delay_start->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $delay->delay_duration }} minutes
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $delay->reason }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
