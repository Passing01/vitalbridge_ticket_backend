@extends('layouts.app-dashboard')

@section('content')
@php
    $date = $date ?? now();
@endphp

<div class="w-full px-6 py-6 mx-auto">
    <div class="flex flex-wrap -mx-3">
        <div class="w-full max-w-full px-3 mb-6">
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                    <div class="flex flex-wrap -mx-3">
                        <div class="flex items-center w-full max-w-full px-3 md:w-5/12">
                            <h6 class="mb-0">Gestion des files d'attente</h6>
                            <p class="text-sm text-slate-500 ml-2 hidden md:block">Suivi des patients en attente et en consultation</p>
                        </div>
                        
                        <!-- Formulaire de recherche -->
                        <div class="w-full md:w-3/12 px-3 mb-4 md:mb-0">
                            <form method="GET" action="{{ route('queues.index') }}" class="relative">
                                <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                                <input type="text" 
                                       name="search" 
                                       value="{{ $search ?? '' }}" 
                                       class="w-full pl-10 pr-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="Rechercher un médecin...">
                                <div class="absolute left-3 top-2.5 text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </form>
                        </div>
                        
                        <div class="flex items-center justify-end w-full md:w-4/12 px-3">
                            <form method="GET" action="{{ route('queues.index') }}" class="flex items-center mr-4">
                                <input type="hidden" name="search" value="{{ $search ?? '' }}">
                                <input type="date" 
                                       name="date" 
                                       class="text-sm rounded-lg border border-gray-300 p-2 mr-2" 
                                       value="{{ $date->format('Y-m-d') }}"
                                       onchange="this.form.submit()"
                                       style="width: 150px;">
                                <a href="{{ route('queues.index') }}" class="p-2 text-gray-600 hover:text-gray-900" title="Réinitialiser">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </a>
                            </form>
                            <span class="px-3 py-1 text-sm font-medium text-gray-700 bg-gray-100 rounded-full whitespace-nowrap">
                                <i class="far fa-calendar-alt mr-1"></i>
                                {{ $date->isoFormat('D MMM YYYY') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex-auto p-4">
                    @if (session('success'))
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                            <span class="font-medium">Succès !</span> {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                            <span class="font-medium">Erreur !</span> {{ session('error') }}
                        </div>
                    @endif

                    @if($doctors->count() > 0)
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Médecin</th>
                                        <th scope="col" class="px-6 py-3">Spécialité</th>
                                        <th scope="col" class="px-6 py-3 text-center">En attente</th>
                                        <th scope="col" class="px-6 py-3 text-center">En cours</th>
                                        <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($doctors as $doctor)
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                        </svg>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $doctor->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 font-medium">{{ $doctor->doctorProfile->specialty->name ?? 'Non spécifiée' }}</div>
                                                <div class="text-sm text-gray-500">{{ $doctor->doctorProfile->specialty->department->name ?? '' }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $doctor->pending_count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $doctor->pending_count }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                @if($doctor->doctorAppointments->isNotEmpty())
                                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                        {{ $doctor->doctorAppointments->first()->patient->first_name }}
                                                        {{ $doctor->doctorAppointments->first()->patient->last_name }}
                                                    </span>
                                                @else
                                                    <span class="text-sm text-gray-500">Aucun</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end">
                                                    <a href="{{ route('queues.show', ['doctor' => $doctor, 'date' => $date->format('Y-m-d')]) }}" 
                                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-gradient-to-tl from-blue-500 to-violet-500 hover:opacity-85 transition-all">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                        Voir la file
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $doctors->links() }}
                        </div>
                    @else
                        <div class="p-6 text-center">
                            <p class="text-gray-500">Aucun médecin trouvé pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
