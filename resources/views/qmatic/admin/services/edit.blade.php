@extends('layouts.qmatic')

@section('title', 'Modifier Service - Qmatic')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('qmatic.admin.services.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Modifier le Service: {{ $service->name }}</h1>
    </div>

    <div class="qmatic-card p-6">
        <form action="{{ route('qmatic.admin.services.update', $service) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Code et Nom -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="col-span-1">
                        <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                        <input type="text" name="code" id="code" value="{{ old('code', $service->code) }}" required maxlength="5" placeholder="A" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm uppercase">
                        <p class="mt-1 text-xs text-gray-500">Ex: A, B, C...</p>
                    </div>
                    
                    <div class="col-span-3">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom du service</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>

                <!-- Icône et Image -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700">Icône (FontAwesome)</label>
                        <input type="text" name="icon" id="icon" value="{{ old('icon', $service->icon) }}" placeholder="fas fa-user"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Ex: fas fa-money-bill, fas fa-hospital...</p>
                    </div>
                    
                    <div>
                        <label for="image_url" class="block text-sm font-medium text-gray-700">URL de l'image (Optionnel)</label>
                        <input type="text" name="image_url" id="image_url" value="{{ old('image_url', $service->image_url) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="https://example.com/image.png">
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('description', $service->description) }}</textarea>
                </div>

                <!-- Priorité et Statut -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="priority_order" class="block text-sm font-medium text-gray-700">Ordre d'affichage</label>
                        <input type="number" name="priority_order" id="priority_order" value="{{ old('priority_order', $service->priority_order) }}" min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    
                    <div class="flex items-center h-full pt-6">
                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Service actif
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Horaires d'ouverture -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Horaires d'ouverture</h3>
                    <div class="space-y-4">
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
                            $workingHours = $service->working_hours ?? [];
                        @endphp

                        @foreach($days as $key => $label)
                        @php
                            $isOpen = isset($workingHours[$key]);
                            $start = $isOpen ? $workingHours[$key]['start'] : '08:00';
                            $end = $isOpen ? $workingHours[$key]['end'] : '17:00';
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                            <div class="flex items-center">
                                <input type="checkbox" name="working_hours[{{ $key }}][active]" id="day_{{ $key }}" value="1" 
                                    {{ $isOpen ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="day_{{ $key }}" class="ml-2 block text-sm font-medium text-gray-700">{{ $label }}</label>
                            </div>
                            
                            <div class="col-span-1">
                                <input type="time" name="working_hours[{{ $key }}][start]" value="{{ $start }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            
                            <div class="text-center text-gray-500">à</div>
                            
                            <div class="col-span-1">
                                <input type="time" name="working_hours[{{ $key }}][end]" value="{{ $end }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 flex justify-end gap-3">
                    <a href="{{ route('qmatic.admin.services.index') }}" class="btn-qmatic bg-gray-500 hover:bg-gray-600">
                        Annuler
                    </a>
                    <button type="submit" class="btn-qmatic">
                        Mettre à jour le service
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
