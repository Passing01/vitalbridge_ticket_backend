@extends('layouts.app-dashboard')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <h1 class="text-2xl font-semibold mb-4">Centres de santé (réceptions)</h1>

        <table class="min-w-full bg-white shadow rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-gray-100 text-left text-xs font-semibold uppercase tracking-wider">
                    <th class="px-4 py-2">Nom</th>
                    <th class="px-4 py-2">Téléphone</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Statut</th>
                    <th class="px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($healthCenters as $center)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $center->full_name }}</td>
                        <td class="px-4 py-2">{{ $center->phone }}</td>
                        <td class="px-4 py-2">{{ $center->email }}</td>
                        <td class="px-4 py-2">
                            @if($center->is_active)
                                <span class="text-green-600 font-semibold">Actif</span>
                            @else
                                <span class="text-red-600 font-semibold">Inactif</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 space-x-2">
                            <a href="{{ route('admin.health-centers.show', $center->id) }}" class="text-blue-600 hover:underline">Détails</a>

                            <form action="{{ route('admin.health-centers.toggle-active', $center->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-yellow-600 hover:underline">
                                    {{ $center->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>

                            <form action="{{ route('admin.health-centers.update-password', $center->id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="password" value="changeme123">
                                <input type="hidden" name="password_confirmation" value="changeme123">
                                <button type="submit" class="text-sm text-red-600 hover:underline">
                                    Réinit. mot de passe (changeme123)
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">Aucun centre de santé trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
