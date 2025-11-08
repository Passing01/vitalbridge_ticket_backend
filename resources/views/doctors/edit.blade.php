@extends('layouts.app-dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Modifier le médecin</h1>
        <p class="text-gray-600">Mettez à jour les informations du Dr {{ $doctor->first_name }} {{ $doctor->last_name }}</p>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('doctors.update', $doctor) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations personnelles -->
                <div class="space-y-4">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2">Informations personnelles</h2>
                    
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">Prénom *</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $doctor->first_name) }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Nom *</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $doctor->last_name) }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $doctor->email) }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Téléphone *</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone', $doctor->phone) }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="profile_photo" class="block text-sm font-medium text-gray-700">Photo de profil</label>
                        <div class="mt-1 flex items-center">
                            <div class="relative">
                                @if($doctor->profile_photo_path)
                                    <img src="{{ asset('storage/' . $doctor->profile_photo_path) }}" alt="Photo de profil" class="h-12 w-12 rounded-full object-cover">
                                @else
                                    <span class="inline-block h-12 w-12 rounded-full overflow-hidden bg-gray-100">
                                        <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </span>
                                @endif
                                <button type="button" id="remove_photo" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" {{ !$doctor->profile_photo_path ? 'hidden' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                            <div class="ml-4">
                                <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                                    class="block text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF jusqu'à 2MB</p>
                                <input type="hidden" name="remove_photo" id="remove_photo_input" value="0">
                            </div>
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
                                <optgroup label="{{ $department->name }}">
                                    @foreach($department->specialties as $specialty)
                                        <option value="{{ $specialty->id }}" 
                                            {{ old('specialty_id', $doctor->doctorProfile->specialty_id ?? '') == $specialty->id ? 'selected' : '' }}>
                                            {{ $specialty->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <input type="hidden" name="specialty_id" id="specialty_id" value="{{ old('specialty_id', $doctor->doctorProfile->specialty_id ?? '') }}">
                        @error('specialty_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="qualification" class="block text-sm font-medium text-gray-700">Diplôme/Qualification *</label>
                        <input type="text" name="qualification" id="qualification" 
                            value="{{ old('qualification', $doctor->doctorProfile->qualification ?? '') }}" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        @error('qualification')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="max_patients_per_day" class="block text-sm font-medium text-gray-700">Patients/jour</label>
                            <input type="number" name="max_patients_per_day" id="max_patients_per_day" 
                                value="{{ old('max_patients_per_day', $doctor->doctorProfile->max_patients_per_day ?? 20) }}" 
                                min="1"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('max_patients_per_day')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="average_consultation_time" class="block text-sm font-medium text-gray-700">Durée consultation (min)</label>
                            <input type="number" name="average_consultation_time" id="average_consultation_time" 
                                value="{{ old('average_consultation_time', $doctor->doctorProfile->average_consultation_time ?? 30) }}" 
                                min="5" max="120"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            @error('average_consultation_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="is_available" value="0">
                        <input type="checkbox" name="is_available" id="is_available" 
                            value="1"
                            {{ old('is_available', $doctor->doctorProfile->is_available ?? true) ? 'checked' : '' }}
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_available" class="ml-2 block text-sm text-gray-700">
                            Disponible pour les rendez-vous
                        </label>
                        @error('is_available')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700">Biographie</label>
                        <textarea name="bio" id="bio" rows="3"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('bio', $doctor->doctorProfile->bio ?? '') }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('doctors.show', $doctor) }}" 
                    class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Annuler
                </a>
                <button type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Gestion de la suppression de la photo de profil
    document.addEventListener('DOMContentLoaded', function() {
        const removePhotoBtn = document.getElementById('remove_photo');
        const removePhotoInput = document.getElementById('remove_photo_input');
        const profilePhotoInput = document.getElementById('profile_photo');
        const profilePhotoPreview = document.querySelector('.relative img');
        const defaultAvatar = document.querySelector('.relative svg');

        if (removePhotoBtn) {
            removePhotoBtn.addEventListener('click', function() {
                // Afficher l'avatar par défaut
                if (profilePhotoPreview) {
                    profilePhotoPreview.style.display = 'none';
                }
                if (defaultAvatar) {
                    defaultAvatar.parentElement.style.display = 'inline-block';
                }
                
                // Cacher le bouton de suppression
                removePhotoBtn.style.display = 'none';
                
                // Indiquer que la photo doit être supprimée
                removePhotoInput.value = '1';
                
                // Réinitialiser le champ de fichier
                if (profilePhotoInput) {
                    profilePhotoInput.value = '';
                }
            });
        }

        // Afficher un aperçu de la nouvelle image sélectionnée
        if (profilePhotoInput) {
            profilePhotoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Afficher la nouvelle image
                        if (!profilePhotoPreview) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = 'Nouvelle photo de profil';
                            img.className = 'h-12 w-12 rounded-full object-cover';
                            document.querySelector('.relative').prepend(img);
                        } else {
                            profilePhotoPreview.src = e.target.result;
                            profilePhotoPreview.style.display = 'block';
                        }
                        
                        // Cacher l'avatar par défaut
                        if (defaultAvatar) {
                            defaultAvatar.parentElement.style.display = 'none';
                        }
                        
                        // Afficher le bouton de suppression
                        if (removePhotoBtn) {
                            removePhotoBtn.style.display = 'block';
                        }
                        
                        // Réinitialiser l'indicateur de suppression
                        removePhotoInput.value = '0';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

    // Mise à jour dynamique des spécialités en fonction du département sélectionné
    document.addEventListener('DOMContentLoaded', function() {
        const departmentSelect = document.getElementById('department_id');
        const specialtyInput = document.getElementById('specialty_id');
        
        // Initialiser avec la spécialité actuelle
        if (departmentSelect && specialtyInput) {
            updateDepartmentSelection();
            updateSpecialtyInput();
            
            // Mettre à jour quand le département change
            departmentSelect.addEventListener('change', updateSpecialtyInput);
        }
        
        function updateDepartmentSelection() {
            // Trouver le département de la spécialité actuelle
            const currentSpecialtyId = specialtyInput.value;
            if (!currentSpecialtyId) return;
            
            // Parcourir les options pour trouver la spécialité actuelle
            const options = departmentSelect.querySelectorAll('option');
            for (const option of options) {
                if (option.value === currentSpecialtyId) {
                    // Sélectionner le département parent
                    const optgroup = option.parentElement;
                    if (optgroup.tagName === 'OPTGROUP') {
                        departmentSelect.value = optgroup.getAttribute('label');
                    }
                    break;
                }
            }
        }
        
        function updateSpecialtyInput() {
            const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
            if (selectedOption && selectedOption.parentElement.tagName === 'OPTGROUP') {
                // Si une spécialité est déjà sélectionnée et appartient à ce département, on la garde
                const currentSpecialtyId = specialtyInput.value;
                const currentOption = departmentSelect.querySelector(`option[value="${currentSpecialtyId}"]`);
                
                if (currentOption && currentOption.parentElement === selectedOption.parentElement) {
                    return; // La spécialité sélectionnée appartient déjà à ce département
                }
                
                // Sinon, sélectionner la première spécialité du département
                const firstSpecialty = selectedOption.parentElement.querySelector('option');
                if (firstSpecialty) {
                    specialtyInput.value = firstSpecialty.value;
                }
            }
        }
    });
</script>
@endpush
@endsection
