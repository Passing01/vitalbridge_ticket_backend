@extends('layouts.qmatic')

@section('title', 'Nouveau Service - Qmatic')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('qmatic.admin.services.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Nouveau Service</h1>
    </div>

    <div class="qmatic-card p-6">
        <form action="{{ route('qmatic.admin.services.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <!-- Code et Nom -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="col-span-1">
                        <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" required maxlength="5" placeholder="A" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm uppercase">
                        <p class="mt-1 text-xs text-gray-500">Ex: A, B, C...</p>
                    </div>
                    
                    <div class="col-span-3">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nom du service</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>

                <!-- Icône et Image -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="icon" class="block text-sm font-medium text-gray-700">Icône (FontAwesome)</label>
                        <input type="text" name="icon" id="icon" value="{{ old('icon') }}" placeholder="fas fa-user"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Ex: fas fa-money-bill, fas fa-hospital...</p>
                    </div>
                    
                    <div>
                        <label for="image_url" class="block text-sm font-medium text-gray-700">URL de l'image (Optionnel)</label>
                        <input type="text" name="image_url" id="image_url" value="{{ old('image_url') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="https://example.com/image.png">
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('description') }}</textarea>
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
                        @endphp

                        @foreach($days as $key => $label)
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                            <div class="flex items-center">
                                <input type="checkbox" name="working_hours[{{ $key }}][active]" id="day_{{ $key }}" value="1" 
                                    {{ in_array($key, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday']) ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="day_{{ $key }}" class="ml-2 block text-sm font-medium text-gray-700">{{ $label }}</label>
                            </div>
                            
                            <div class="col-span-1">
                                <input type="time" name="working_hours[{{ $key }}][start]" value="08:00"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                            
                            <div class="text-center text-gray-500">à</div>
                            
                            <div class="col-span-1">
                                <input type="time" name="working_hours[{{ $key }}][end]" value="17:00"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 flex justify-end">
                    <button type="submit" class="btn-qmatic">
                        Créer le service
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
