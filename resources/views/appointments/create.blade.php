@extends('layouts.app-dashboard')

@section('content')
<div class="w-full px-6 py-6 mx-auto">
    <div class="flex flex-wrap -mx-3">
        <div class="w-full max-w-full px-3 mb-6">
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                    <div class="flex flex-wrap -mx-3">
                        <div class="flex items-center w-full max-w-full px-3 md:w-8/12">
                            <h6 class="mb-0">Créer un rendez-vous</h6>
                            <p class="text-sm text-slate-500 ml-2">Planifier un rendez-vous pour un patient avec le Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}</p>
                        </div>
                        <div class="w-full max-w-full px-3 text-right md:w-4/12">
                            <a href="{{ url()->previous() }}"
                               class="inline-block px-6 py-3 mb-0 ml-6 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-red-600 to-pink-400 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md">
                                Retour
                            </a>
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

                    @if ($errors->any())
                        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('queues.appointments.store') }}" class="space-y-6">
                        @csrf

                        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                                <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-800">
                                    Dr. {{ $doctor->first_name }} {{ $doctor->last_name }}
                                    @if($doctor->doctorProfile && $doctor->doctorProfile->specialty)
                                        <span class="text-gray-500 text-xs">- {{ $doctor->doctorProfile->specialty->name }}</span>
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-1">Patient</label>
                                <select id="patient_id" name="patient_id" class="block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Sélectionner un patient</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}" @selected(old('patient_id', request('patient')) == $patient->id)>
                                            {{ $patient->first_name }} {{ $patient->last_name }} - {{ $patient->email }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Laissez vide pour créer un nouveau patient ci-dessous.</p>
                            </div>
                        </div>

                        <div class="mt-4 border-t border-dashed border-gray-200 pt-4">
                            <p class="text-sm font-semibold text-gray-700 mb-2">Ou nouveau patient</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="new_patient_first_name" class="block text-sm font-medium text-gray-700 mb-1">Prénom du patient</label>
                                    <input type="text" id="new_patient_first_name" name="new_patient_first_name" value="{{ old('new_patient_first_name') }}" class="block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="new_patient_last_name" class="block text-sm font-medium text-gray-700 mb-1">Nom du patient</label>
                                    <input type="text" id="new_patient_last_name" name="new_patient_last_name" value="{{ old('new_patient_last_name') }}" class="block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                                <div>
                                    <label for="new_patient_phone" class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                    <input type="text" id="new_patient_phone" name="new_patient_phone" value="{{ old('new_patient_phone') }}" class="block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="new_patient_email" class="block text-sm font-medium text-gray-700 mb-1">Email (optionnel)</label>
                                    <input type="email" id="new_patient_email" name="new_patient_email" value="{{ old('new_patient_email') }}" class="block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Si aucun patient existant n'est sélectionné, ces informations seront utilisées pour créer un nouveau patient interne (sans compte).</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <input type="date" id="appointment_date" name="appointment_date" value="{{ old('appointment_date', $date->format('Y-m-d')) }}" class="block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label for="appointment_time" class="block text-sm font-medium text-gray-700 mb-1">Heure</label>
                                <select id="appointment_time" name="appointment_time" class="block w-full rounded-lg border-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    @forelse($availableSlots as $slot)
                                        @foreach($slot['slots'] as $time)
                                            <option value="{{ $slot['date'] . ' ' . $time['time'] }}" @selected(old('appointment_date') == ($slot['date'] . ' ' . $time['time']))>
                                                {{ $slot['date'] }} - {{ $time['formatted_time'] }}
                                            </option>
                                        @endforeach
                                    @empty
                                        <option value="">Aucun créneau disponible</option>
                                    @endforelse
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Les créneaux sont générés à partir du planning et des indisponibilités du médecin.</p>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <input id="is_urgent" name="is_urgent" type="checkbox" value="1" class="h-4 w-4 text-red-600 border-gray-300 rounded">
                            <label for="is_urgent" class="ml-2 block text-sm text-gray-700">
                                Rendez-vous urgent (le patient sera placé en tête de file)
                            </label>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-7 py-3 border border-blue-500 text-sm font-semibold rounded-lg text-white bg-gradient-to-tl from-blue-500 to-violet-500 hover:opacity-90 shadow-soft-xl focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-save mr-2"></i> Enregistrer le rendez-vous
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
