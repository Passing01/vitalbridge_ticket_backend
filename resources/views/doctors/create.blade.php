@extends('layouts.app-dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ajouter un nouveau médecin</h1>
        <p class="text-gray-600">Remplissez les informations ci-dessous pour ajouter un nouveau médecin.</p>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('doctors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations personnelles -->
                <div class="space-y-4">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2">Informations personnelles</h2>
                    
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom *</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Nom *</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone *</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="profile_photo" class="block text-sm font-medium text-gray-700">Photo de profil</label>
                        <div class="mt-1 flex items-center">
                            <span class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                                <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </span>
                            <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                                class="ml-5 block text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        @error('profile_photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Informations professionnelles -->
                <div class="space-y-4">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2">Informations professionnelles</h2>
                    
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700">Département *</label>
                        <select name="department_id" id="department_id" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionnez un département</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="specialty_id" class="block text-sm font-medium text-gray-700">Spécialité *</label>
                        <select name="specialty_id" id="specialty_id" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Sélectionnez d'abord un département</option>
                        </select>
                        @error('specialty_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="qualification" class="block text-sm font-medium text-gray-700">Diplôme/Qualification *</label>
                        <input type="text" name="qualification" id="qualification" value="{{ old('qualification') }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('qualification')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="max_patients_per_day" class="block text-sm font-medium text-gray-700">Patients/jour</label>
                            <input type="number" name="max_patients_per_day" id="max_patients_per_day" 
                                value="{{ old('max_patients_per_day', 20) }}" min="1"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('max_patients_per_day')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="average_consultation_time" class="block text-sm font-medium text-gray-700">Durée consultation (min)</label>
                            <input type="number" name="average_consultation_time" id="average_consultation_time" 
                                value="{{ old('average_consultation_time', 30) }}" min="5" max="120"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('average_consultation_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700">Biographie</label>
                        <textarea name="bio" id="bio" rows="3"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('bio') }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('doctors.index') }}" 
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Annuler
                </a>
                <button type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Enregistrer le médecin
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const departmentSelect = document.getElementById('department_id');
        const specialtySelect = document.getElementById('specialty_id');
        
        // Fonction pour charger les spécialités d'un département
        function loadSpecialties(departmentId) {
            if (!departmentId) {
                specialtySelect.innerHTML = '<option value="">Sélectionnez d\'abord un département</option>';
                return;
            }

            // Afficher un indicateur de chargement
            specialtySelect.disabled = true;
            specialtySelect.innerHTML = '<option value="">Chargement des spécialités...</option>';

            // Récupérer les spécialités via une requête AJAX
            console.log(`Chargement des spécialités pour le département ${departmentId}...`);
            
            fetch(`/departments/${departmentId}/specialties`)
                .then(response => {
                    console.log('Réponse reçue, statut:', response.status);
                    if (!response.ok) {
                        throw new Error(`Erreur HTTP! Statut: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Données reçues:', data);
                    
                    if (!data.success) {
                        throw new Error(data.message || 'Erreur lors du chargement des spécialités');
                    }
                    
                    const specialties = data.data || [];
                    let options = '<option value="">Sélectionnez une spécialité</option>';
                    
                    if (specialties.length > 0) {
                        specialties.forEach(specialty => {
                            const selected = specialty.id == '{{ old('specialty_id') }}' ? 'selected' : '';
                            options += `<option value="${specialty.id}" ${selected}>${specialty.name}</option>`;
                        });
                    } else {
                        options = '<option value="">Aucune spécialité disponible pour ce département</option>';
                    }
                    
                    specialtySelect.innerHTML = options;
                    specialtySelect.disabled = false;
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des spécialités:', error);
                    specialtySelect.innerHTML = `<option value="">Erreur: ${error.message}</option>`;
                    specialtySelect.disabled = false;
                });
        }

        // Charger les spécialités lorsque le département change
        if (departmentSelect && specialtySelect) {
            console.log('Éléments trouvés:', { departmentSelect, specialtySelect });
            
            // Ajouter l'écouteur d'événement pour le changement de département
            departmentSelect.addEventListener('change', function() {
                console.log('Changement de département détecté:', this.value);
                loadSpecialties(this.value);
            });
            
            // Charger les spécialités du département sélectionné si un département est déjà sélectionné
            if (departmentSelect.value) {
                console.log('Département présélectionné:', departmentSelect.value);
                loadSpecialties(departmentSelect.value);
            }
        }
    });
</script>
@endpush
@endsection
