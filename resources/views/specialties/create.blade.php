@extends('layouts.app-dashboard')

@section('content')
<div class="w-full px-6 py-6 mx-auto">
    <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
        <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
            <h6 class="mb-0">Ajouter des spécialités</h6>
            <p class="text-sm text-slate-500">Remplissez les informations pour ajouter une ou plusieurs spécialités</p>
        </div>
        <div class="flex-auto p-6">
            <form id="specialtiesForm" action="{{ route('specialties.store') }}" method="POST">
                @csrf
                
                <div class="mb-6">
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-2">Département *</label>
                    <select name="department_id" id="department_id" required
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sélectionnez un département</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="specialties-container" class="space-y-4">
                    <!-- Les champs de spécialité seront ajoutés ici dynamiquement -->
                    <div class="specialty-group border border-gray-200 rounded-lg p-4 bg-gray-50" data-index="0">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-sm font-medium text-gray-700">Spécialité #1</h4>
                            <button type="button" class="remove-specialty text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                                <input type="text" name="specialties[0][name]" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                    required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <input type="text" name="specialties[0][description]" 
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="button" id="addSpecialty" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i> Ajouter une autre spécialité
                    </button>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('specialties.index') }}" 
                        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Annuler
                    </a>
                    <button type="submit" 
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Enregistrer les spécialités
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('specialties-container');
        const addButton = document.getElementById('addSpecialty');
        let index = 1;

        // Ajouter un nouveau groupe de champs
        function addSpecialtyField() {
            const newGroup = document.createElement('div');
            newGroup.className = 'specialty-group border border-gray-200 rounded-lg p-4 bg-gray-50';
            newGroup.setAttribute('data-index', index);
            
            newGroup.innerHTML = `
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-sm font-medium text-gray-700">Spécialité #${index + 1}</h4>
                    <button type="button" class="remove-specialty text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                        <input type="text" name="specialties[${index}][name]" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" name="specialties[${index}][description]" 
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            `;
            
            container.appendChild(newGroup);
            index++;
            updateRemoveButtons();
        }

        // Mettre à jour les boutons de suppression
        function updateRemoveButtons() {
            const removeButtons = document.querySelectorAll('.remove-specialty');
            removeButtons.forEach(button => {
                button.style.display = removeButtons.length > 1 ? 'block' : 'none';
            });
        }

        // Gérer l'ajout d'une nouvelle spécialité
        addButton.addEventListener('click', addSpecialtyField);

        // Gérer la suppression d'une spécialité
        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-specialty')) {
                const group = e.target.closest('.specialty-group');
                if (container.querySelectorAll('.specialty-group').length > 1) {
                    group.remove();
                    updateRemoveButtons();
                }
            }
        });

        // Validation du formulaire
        const form = document.getElementById('specialtiesForm');
        form.addEventListener('submit', function(e) {
            const departmentSelect = document.getElementById('department_id');
            if (!departmentSelect.value) {
                e.preventDefault();
                alert('Veuillez sélectionner un département.');
                departmentSelect.focus();
                return false;
            }

            const nameInputs = document.querySelectorAll('input[name^="specialties"][name$="[name]"]');
            let hasError = false;
            const names = new Set();

            nameInputs.forEach((input, i) => {
                if (!input.value.trim()) {
                    hasError = true;
                    input.classList.add('border-red-500');
                    const group = input.closest('.specialty-group');
                    const errorDiv = document.createElement('p');
                    errorDiv.className = 'mt-1 text-sm text-red-600';
                    errorDiv.textContent = 'Le nom de la spécialité est requis.';
                    
                    // Supprimer les anciens messages d'erreur
                    const existingError = group.querySelector('.text-red-600');
                    if (!existingError) {
                        input.after(errorDiv);
                    }
                } else {
                    input.classList.remove('border-red-500');
                    const existingError = input.nextElementSibling;
                    if (existingError && existingError.classList.contains('text-red-600')) {
                        existingError.remove();
                    }

                    // Vérifier les doublons
                    if (names.has(input.value.toLowerCase())) {
                        hasError = true;
                        input.classList.add('border-red-500');
                        const errorDiv = document.createElement('p');
                        errorDiv.className = 'mt-1 text-sm text-red-600';
                        errorDiv.textContent = 'Cette spécialité a déjà été ajoutée.';
                        input.after(errorDiv);
                    } else {
                        names.add(input.value.toLowerCase());
                    }
                }
            });

            if (hasError) {
                e.preventDefault();
                return false;
            }
        });

        // Initialiser les boutons de suppression
        updateRemoveButtons();
    });
</script>
@endpush
@endsection