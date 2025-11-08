@extends('layouts.app-dashboard')

@section('content')
<div class="w-full px-6 py-6 mx-auto">
    <div class="flex flex-wrap -mx-3">
        <!-- Colonne principale -->
        <div class="w-full max-w-full px-3 lg:w-8/12">
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border mb-6">
                <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                    <div class="flex flex-wrap -mx-3">
                        <div class="flex items-center w-full max-w-full px-3 md:w-8/12">
                            <h6 class="mb-0">
                                <i class="fas fa-user-md text-slate-600 me-2"></i>
                                Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                                <span class="text-sm text-slate-500">{{ $doctor->doctorProfile->specialty->name ?? 'Spécialité non définie' }}</span>
                            </h6>
                           <a href="{{ route('queues.index') }}" class="inline-block px-6 py-3 mb-0 ml-2 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-red-600 to-pink-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md bg-150 bg-x-25">
                            <i class="fas fa-calendar-alt mr-2"></i>Retour
                        </a>
                        </div>
                        <div class="flex items-center justify-end w-full max-w-full px-3 md:w-4/12">
                            <form method="GET" class="flex items-center mr-4">
                                <input type="date" 
                                       name="date" 
                                       class="text-sm rounded-lg border border-gray-300 p-2 mr-2" 
                                       value="{{ $date->format('Y-m-d') }}"
                                       onchange="this.form.submit()"
                                       style="width: 150px;">
                                <a href="{{ route('queues.show', $doctor) }}" class="p-2 text-gray-600 hover:text-gray-900">
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
                    @if(session('success'))
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                            <span class="font-medium">Succès !</span> {{ session('success') }}
                        </div>
                    @elseif(session('warning'))
                        <div class="mb-4 p-4 text-sm text-yellow-700 bg-yellow-100 rounded-lg" role="alert">
                            <span class="font-medium">Attention !</span> {{ session('warning') }}
                        </div>
                    @endif

                    <!-- Patient en cours -->
                    @if($current)
                        <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-xl shadow-sm">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="mb-3 md:mb-0">
                                    <div class="flex items-center">
                                        <div class="p-2 mr-3 bg-green-100 rounded-full">
                                            <i class="fas fa-user-injured text-green-600"></i>
                                        </div>
                                        <div>
                                            <h5 class="text-lg font-semibold text-gray-800">
                                                {{ $current->patient->first_name }} {{ $current->patient->last_name }}
                                                @if($current->is_urgent)
                                                    <span class="ml-2 px-2 py-0.5 text-xs font-semibold text-white bg-red-500 rounded-full">Urgent</span>
                                                @endif
                                            </h5>
                                            <p class="flex items-center text-sm text-gray-600">
                                                <i class="far fa-clock mr-1.5 text-green-500"></i> 
                                                <span>Début: <span class="font-medium">{{ $current->start_time->format('H:i') }}</span></span>
                                                @if($current->delay_minutes > 0)
                                                    <span class="ml-3 px-2 py-0.5 text-xs font-medium text-yellow-800 bg-yellow-100 rounded-full">
                                                        <i class="fas fa-clock mr-1"></i> Retard: {{ $current->delay_minutes }} min
                                                    </span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <button onclick="event.preventDefault(); document.getElementById('end-appointment-{{ $current->id }}').submit();" 
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all">
                                        <i class="fas fa-check-circle mr-1.5"></i> Terminer la consultation
                                    </button>
                                    <form id="end-appointment-{{ $current->id }}" action="{{ route('appointments.end', $current) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('PATCH')
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- File d'attente -->
                    <div class="mb-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                            <h5 class="flex items-center text-lg font-semibold text-gray-800 mb-3 sm:mb-0">
                                <i class="fas fa-list-ol text-blue-500 mr-2"></i>
                                File d'attente
                                <span class="ml-2 px-2.5 py-0.5 text-xs font-semibold text-white bg-blue-500 rounded-full">
                                    {{ $pending->count() }}
                                </span>
                            </h5>
                            @if($pending->isNotEmpty())
                                <button type="button" 
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#delayModal" 
                                        {{ $current ? '' : 'disabled' }}>
                                    <i class="fas fa-clock mr-1.5"></i> Déclarer un retard
                                </button>
                            @endif
                        </div>
                        
                        <div class="space-y-3">
                            @forelse($pending as $appointment)
                                <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                        <div class="mb-3 sm:mb-0">
                                            <div class="flex items-start">
                                                <div class="p-2 mr-3 bg-blue-50 rounded-lg">
                                                    <i class="fas fa-user-injured text-blue-500"></i>
                                                </div>
                                                <div>
                                                    <h6 class="font-medium text-gray-900 flex items-center">
                                                        <a href="#" class="hover:text-blue-600 transition-colors" 
                                                           onclick="event.preventDefault(); showAppointmentDetails({{ $appointment->id }});">
                                                            {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                                                        </a>
                                                        @if($appointment->is_urgent)
                                                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold text-white bg-red-500 rounded-full">Urgent</span>
                                                        @endif
                                                    </h6>
                                                    <div class="flex flex-wrap items-center mt-1 text-sm text-gray-500">
                                                        <span class="flex items-center mr-4">
                                                            <i class="far fa-clock mr-1.5 text-blue-400"></i>
                                                            {{ $appointment->appointment_date->format('H:i') }}
                                                        </span>
                                                        @if($appointment->delay_minutes > 0)
                                                            <span class="flex items-center text-yellow-700 bg-yellow-100 px-2 py-0.5 rounded-full text-xs font-medium">
                                                                <i class="fas fa-clock mr-1"></i> Retard: {{ $appointment->delay_minutes }} min
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <button type="button" 
                                                    class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-colors"
                                                    onclick="event.preventDefault(); showAppointmentDetails({{ $appointment->id }});"
                                                    title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            @can('update', $appointment)
                                                <button type="button" 
                                                        class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-full transition-colors {{ $current ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                        onclick="event.preventDefault(); {{ !$current ? 'document.getElementById(\'start-appointment-' . $appointment->id . '\').submit();' : '' }}"
                                                        {{ $current ? 'disabled' : '' }}
                                                        title="Commencer la consultation">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                                <form id="start-appointment-{{ $appointment->id }}" action="{{ route('queues.appointments.start', $appointment) }}" method="POST" class="hidden">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                                
                                                <button type="button" 
                                                        class="p-2 text-gray-500 hover:text-yellow-600 hover:bg-yellow-50 rounded-full transition-colors"
                                                        onclick="event.preventDefault(); document.getElementById('mark-absent-{{ $appointment->id }}').submit();"
                                                        title="Marquer comme absent">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                                <form id="mark-absent-{{ $appointment->id }}" action="{{ route('queues.appointments.absent', $appointment) }}" method="POST" class="hidden">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center bg-blue-50 rounded-lg">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-3xl text-blue-400 mb-2"></i>
                                        <p class="text-gray-600">Aucun patient en attente pour le moment.</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Patients absents -->
                    @if($absent->isNotEmpty())
                        <div class="mb-6">
                            <h5 class="flex items-center text-lg font-semibold text-gray-800 mb-4">
                                <i class="fas fa-user-times text-gray-500 mr-2"></i>
                                Patients absents
                                <span class="ml-2 px-2.5 py-0.5 text-xs font-semibold text-white bg-gray-500 rounded-full">
                                    {{ $absent->count() }}
                                </span>
                            </h5>
                            
                            <div class="space-y-3">
                                @foreach($absent as $appointment)
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                            <div class="mb-3 sm:mb-0">
                                                <div class="flex items-start">
                                                    <div class="p-2 mr-3 bg-gray-100 rounded-lg">
                                                        <i class="fas fa-user-slash text-gray-400"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="font-medium text-gray-700">
                                                            {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                                                        </h6>
                                                        <div class="flex flex-wrap items-center mt-1 text-sm text-gray-500">
                                                            <span class="flex items-center mr-4">
                                                                <i class="far fa-clock mr-1.5 text-gray-400"></i>
                                                                {{ $appointment->appointment_date->format('H:i') }}
                                                            </span>
                                                            <span class="flex items-center text-red-700 bg-red-100 px-2 py-0.5 rounded-full text-xs font-medium">
                                                                <i class="fas fa-user-slash mr-1"></i> Absent
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center space-x-1">
                                                <button type="button" 
                                                        class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-colors"
                                                        onclick="event.preventDefault(); showAppointmentDetails({{ $appointment->id }});"
                                                        title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                
                                                @can('update', $appointment)
                                                    <button type="button" 
                                                            class="p-2 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-full transition-colors"
                                                            onclick="event.preventDefault(); document.getElementById('mark-present-{{ $appointment->id }}').submit();"
                                                            title="Marquer comme présent">
                                                        <i class="fas fa-user-check"></i>
                                                    </button>
                                                    <form id="mark-present-{{ $appointment->id }}" action="{{ route('appointments.present', $appointment) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('PATCH')
                                                    </form>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Patients déjà vus -->
                    @if($served->isNotEmpty())
                        <div class="mb-6">
                            <h5 class="flex items-center text-lg font-semibold text-gray-800 mb-4">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                Déjà vus aujourd'hui
                                <span class="ml-2 px-2.5 py-0.5 text-xs font-semibold text-white bg-green-500 rounded-full">
                                    {{ $served->count() }}
                                </span>
                            </h5>
                            
                            <div class="space-y-3">
                                @foreach($served as $appointment)
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-sm transition-shadow duration-200">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                            <div class="mb-3 sm:mb-0">
                                                <div class="flex items-start">
                                                    <div class="p-2 mr-3 bg-green-50 rounded-lg">
                                                        <i class="fas fa-check text-green-500"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="font-medium text-gray-900">
                                                            {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                                                        </h6>
                                                        <div class="flex flex-wrap items-center mt-1 text-sm text-gray-500">
                                                            <span class="flex items-center mr-4">
                                                                <i class="far fa-clock mr-1.5 text-green-400"></i>
                                                                {{ $appointment->appointment_date->format('H:i') }}
                                                            </span>
                                                            <span class="flex items-center text-green-700 bg-green-100 px-2 py-0.5 rounded-full text-xs font-medium">
                                                                <i class="fas fa-check-circle mr-1"></i> Consultation terminée
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center">
                                                <button type="button" 
                                                        class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-full transition-colors"
                                                        onclick="event.preventDefault(); showAppointmentDetails({{ $appointment->id }});"
                                                        title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Colonne latérale -->
        <div class="w-full max-w-full px-3 lg:w-4/12">
            <!-- Récapitulatif -->
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border mb-6">
                <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                    <div class="flex items-center">
                        <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                        <h6 class="mb-0">Récapitulatif</h6>
                    </div>
                </div>
                <div class="flex-auto p-4">
                    <div class="mb-6">
                        <h6 class="text-gray-500 text-sm font-semibold mb-4">Statistiques du jour</h6>
                        
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-600">En attente</span>
                            </div>
                            <span class="px-2.5 py-0.5 text-xs font-semibold text-white bg-blue-500 rounded-full">
                                {{ $pending->count() }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-cyan-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-600">En consultation</span>
                            </div>
                            <span class="px-2.5 py-0.5 text-xs font-semibold text-white bg-cyan-500 rounded-full">
                                {{ $current ? 1 : 0 }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between py-2 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-600">Déjà vus</span>
                            </div>
                            <span class="px-2.5 py-0.5 text-xs font-semibold text-white bg-green-500 rounded-full">
                                {{ $served->count() }}
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between py-2">
                            <div class="flex items-center">
                                <div class="w-2 h-2 bg-gray-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-600">Absents</span>
                            </div>
                            <span class="px-2.5 py-0.5 text-xs font-semibold text-white bg-gray-500 rounded-full">
                                {{ $absent->count() }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100">
                        <h6 class="text-gray-500 text-sm font-semibold mb-3">Actions rapides</h6>
                        <div class="space-y-2">
                            <button type="button" 
                                    class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#unavailabilityModal">
                                <i class="fas fa-ban text-gray-500 mr-2"></i>
                                Déclarer une indisponibilité
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->


@endsection

@push('scripts')
<script>
function showAppointmentDetails(appointmentId) {
    console.log('Fonction appelée avec ID:', appointmentId);
    const modal = new bootstrap.Modal(document.getElementById('appointmentDetailsModal'));
    const content = document.getElementById('appointmentDetailsContent');
    
    // Afficher le spinner
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>`;
    
    // Afficher la modale
    modal.show();
    
    // Simuler un chargement pour le test
    setTimeout(() => {
        content.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">
                        <i class="fas fa-user-injured me-2"></i>
                        Informations patient (TEST)
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><strong>ID du rendez-vous :</strong> ${appointmentId}</li>
                        <li class="mb-2"><strong>Nom complet :</strong> Test Patient</li>
                        <li class="mb-2"><strong>Date de naissance :</strong> 01/01/1980</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3">
                        <i class="fas fa-calendar-check me-2"></i>
                        Détails du rendez-vous
                    </h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><strong>Date et heure :</strong> ${new Date().toLocaleString('fr-FR')}</li>
                        <li class="mb-2"><strong>Statut :</strong> <span class="badge bg-primary">Planifié</span></li>
                    </ul>
                </div>
            </div>`;
    }, 500);
}
</script>
@endpush
