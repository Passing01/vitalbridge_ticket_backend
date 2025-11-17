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
                        <div class="flex items-center w-full max-w-full px-3 md:w-8/12">
                            <h6 class="mb-0">Mes patients</h6>
                            <p class="text-sm text-slate-500 ml-2 hidden md:block">Suivi des demandeurs de rendez-vous, patients du jour et historiques</p>
                        </div>
                        <div class="flex flex-col md:flex-row md:items-center md:justify-end w-full max-w-full px-3 md:w-4/12 space-y-2 md:space-y-0 md:space-x-3">
                            <form method="GET" class="flex items-center">
                                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="text-sm rounded-lg border border-gray-300 p-2 mr-2" onchange="this.form.submit()" style="width: 150px;">
                                <span class="px-3 py-1 text-sm font-medium text-gray-700 bg-gray-100 rounded-full whitespace-nowrap">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ $date->isoFormat('D MMM YYYY') }}
                                </span>
                            </form>

                            <div class="flex items-center space-x-2">
                                <select id="filter_specialty" class="text-xs rounded-lg border border-gray-300 p-2">
                                    <option value="">Spécialité</option>
                                    @foreach($specialties as $specialty)
                                        <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                                    @endforeach
                                </select>

                                <select id="filter_doctor" class="text-xs rounded-lg border border-gray-300 p-2">
                                    <option value="">Médecin</option>
                                    @foreach($doctors as $doctorOption)
                                        <option value="{{ $doctorOption->id }}" data-specialty="{{ optional($doctorOption->doctorProfile->specialty)->id }}">
                                            Dr. {{ $doctorOption->first_name }} {{ $doctorOption->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-center space-x-2">
                                <button type="button" id="auto_pick_doctor"
                                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-[11px] font-medium rounded-lg text-gray-700 bg-gray-100 hover:bg-gray-200">
                                    <i class="fas fa-magic mr-1"></i> Choisir un médecin
                                </button>

                                <a href="" id="create_appointment_from_filters"
                                   class="inline-flex items-center px-5 py-2.5 border border-blue-500 text-xs font-semibold rounded-lg text-white bg-gradient-to-tl from-blue-500 to-violet-500 hover:opacity-90 shadow-soft-xl">
                                    <i class="fas fa-plus mr-2"></i> Créer un rendez-vous
                                </a>
                            </div>
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

                    <div class="border-b border-gray-200 mb-4">
                        <nav class="flex space-x-4" aria-label="Tabs">
                            <button type="button" class="tab-link px-3 py-2 text-sm font-medium border-b-2 border-blue-500 text-blue-600" data-target="#tab-requests">Demandes de rendez-vous</button>
                            <button type="button" class="tab-link px-3 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-target="#tab-today">Rendez-vous du jour</button>
                            <button type="button" class="tab-link px-3 py-2 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700" data-target="#tab-past">Historique</button>
                        </nav>
                    </div>

                    <div id="tab-requests" class="tab-panel">
                        @if($allRequests->count() > 0)
                            <div class="relative overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3">Patient</th>
                                            <th class="px-6 py-3">Médecin</th>
                                            <th class="px-6 py-3">Spécialité</th>
                                            <th class="px-6 py-3">Date</th>
                                            <th class="px-6 py-3">Statut</th>
                                            <th class="px-6 py-3 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($allRequests as $appointment)
                                            <tr class="bg-white border-b hover:bg-gray-50">
                                                <td class="px-6 py-4">{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</td>
                                                <td class="px-6 py-4">Dr. {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}</td>
                                                <td class="px-6 py-4">{{ $appointment->specialty->name ?? 'N/A' }}</td>
                                                <td class="px-6 py-4">{{ $appointment->appointment_date->format('d/m/Y H:i') }}</td>
                                                <td class="px-6 py-4">
                                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $appointment->status === 'scheduled' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ $appointment->status }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <a href="{{ route('queues.appointments.create', ['doctor' => $appointment->doctor_id, 'date' => $appointment->appointment_date->format('Y-m-d'), 'patient' => $appointment->patient_id]) }}"
                                                       class="inline-flex items-center px-3.5 py-1.5 border border-blue-500 text-xs font-semibold rounded-lg text-white bg-gradient-to-tl from-blue-500 to-violet-500 hover:opacity-90 shadow-soft-sm">
                                                        <i class="fas fa-calendar-plus mr-1"></i> Nouveau rendez-vous
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Aucune demande de rendez-vous trouvée.</p>
                        @endif
                    </div>

                    <div id="tab-today" class="tab-panel hidden">
                        <h6 class="text-sm font-semibold text-gray-700 mb-3">Rendez-vous du jour</h6>

                        @if($current)
                            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm text-gray-700">
                                    <span class="font-semibold">En consultation :</span>
                                    {{ $current->patient->first_name }} {{ $current->patient->last_name }}
                                    avec Dr. {{ $current->doctor->first_name }} {{ $current->doctor->last_name }}
                                </p>
                            </div>
                        @endif

                        @if($todayPending->count() > 0)
                            <div class="relative overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3">Position</th>
                                            <th class="px-6 py-3">Patient</th>
                                            <th class="px-6 py-3">Médecin</th>
                                            <th class="px-6 py-3">Heure</th>
                                            <th class="px-6 py-3">Urgent</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($todayPending as $appointment)
                                            <tr class="bg-white border-b hover:bg-gray-50">
                                                <td class="px-6 py-4">{{ $appointment->queue_position }}</td>
                                                <td class="px-6 py-4">{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</td>
                                                <td class="px-6 py-4">Dr. {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}</td>
                                                <td class="px-6 py-4">{{ $appointment->appointment_date->format('H:i') }}</td>
                                                <td class="px-6 py-4">
                                                    @if($appointment->is_urgent)
                                                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-red-100 text-red-800">Urgent</span>
                                                    @else
                                                        <span class="text-xs text-gray-500">Normal</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Aucun rendez-vous en attente pour cette date.</p>
                        @endif
                    </div>

                    <div id="tab-past" class="tab-panel hidden">
                        @if($pastAppointments->count() > 0)
                            <div class="relative overflow-x-auto">
                                <table class="w-full text-sm text-left text-gray-500">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3">Patient</th>
                                            <th class="px-6 py-3">Médecin</th>
                                            <th class="px-6 py-3">Date</th>
                                            <th class="px-6 py-3">Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pastAppointments as $appointment)
                                            <tr class="bg-white border-b hover:bg-gray-50">
                                                <td class="px-6 py-4">{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</td>
                                                <td class="px-6 py-4">Dr. {{ $appointment->doctor->first_name }} {{ $appointment->doctor->last_name }}</td>
                                                <td class="px-6 py-4">{{ $appointment->appointment_date->format('d/m/Y H:i') }}</td>
                                                <td class="px-6 py-4">
                                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $appointment->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ $appointment->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">Aucun rendez-vous passé enregistré pour l’instant.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.tab-link').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.tab-link').forEach(function (b) {
                b.classList.remove('border-blue-500', 'text-blue-600');
                b.classList.add('border-transparent', 'text-gray-500');
            });
            document.querySelectorAll('.tab-panel').forEach(function (panel) {
                panel.classList.add('hidden');
            });
            this.classList.add('border-blue-500', 'text-blue-600');
            this.classList.remove('border-transparent', 'text-gray-500');
            const target = document.querySelector(this.dataset.target);
            if (target) {
                target.classList.remove('hidden');
            }
        });
    });

    // Auto-choix du médecin pour une spécialité
    const specialtySelect = document.getElementById('filter_specialty');
    const doctorSelect = document.getElementById('filter_doctor');
    const autoPickBtn = document.getElementById('auto_pick_doctor');
    const createBtn = document.getElementById('create_appointment_from_filters');

    if (autoPickBtn && specialtySelect && doctorSelect) {
        autoPickBtn.addEventListener('click', function () {
            const specialtyId = specialtySelect.value;
            if (!specialtyId) return;

            const options = Array.from(doctorSelect.options).filter(opt => {
                return opt.value && opt.getAttribute('data-specialty') === specialtyId;
            });

            if (options.length > 0) {
                doctorSelect.value = options[0].value;
            }
        });
    }

    if (createBtn && doctorSelect) {
        createBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const doctorId = doctorSelect.value;
            const date = '{{ $date->format('Y-m-d') }}';

            if (!doctorId) {
                alert('Veuillez choisir un médecin (ou une spécialité puis cliquez sur "Choisir un médecin")');
                return;
            }

            const url = `{{ route('queues.appointments.create', ['doctor' => 'DOCTOR_ID', 'date' => 'DATE_PLACEHOLDER']) }}`
                .replace('DOCTOR_ID', doctorId)
                .replace('DATE_PLACEHOLDER', date);

            window.location.href = url;
        });
    }
</script>
@endpush
