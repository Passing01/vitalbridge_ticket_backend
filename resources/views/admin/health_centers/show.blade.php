@extends('layouts.app-dashboard')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-semibold mb-4">Détails du centre de santé</h1>

        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <h2 class="text-xl font-semibold mb-2">{{ $healthCenter->full_name }}</h2>
            <p><strong>Téléphone :</strong> {{ $healthCenter->phone }}</p>
            <p><strong>Email :</strong> {{ $healthCenter->email }}</p>
            <p class="mt-2">
                <strong>Statut :</strong>
                @if($healthCenter->is_active)
                    <span class="text-green-600 font-semibold">Actif</span>
                @else
                    <span class="text-red-600 font-semibold">Inactif</span>
                @endif
            </p>

            <div class="mt-4 flex space-x-4">
                <form action="{{ route('admin.health-centers.toggle-active', $healthCenter->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3 py-1 bg-yellow-500 text-white text-sm rounded">
                        {{ $healthCenter->is_active ? 'Désactiver' : 'Activer' }}
                    </button>
                </form>

                <form action="{{ route('admin.health-centers.update-password', $healthCenter->id) }}" method="POST" class="flex items-center space-x-2">
                    @csrf
                    <input type="password" name="password" placeholder="Nouveau mot de passe" class="border rounded px-2 py-1 text-sm">
                    <input type="password" name="password_confirmation" placeholder="Confirmer" class="border rounded px-2 py-1 text-sm">
                    <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm rounded">
                        Mettre à jour le mot de passe
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-2">Départements gérés</h2>

            @if($healthCenter->managedDepartments->isEmpty())
                <p class="text-gray-500">Aucun département associé.</p>
            @else
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($healthCenter->managedDepartments as $department)
                        <li>
                            <strong>{{ $department->name }}</strong>
                            @if($department->description)
                                - {{ $department->description }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.health-centers.index') }}" class="text-blue-600 hover:underline">&larr; Retour à la liste</a>
        </div>
    </div>
@endsection
