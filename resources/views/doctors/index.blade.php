@extends('layouts.app-dashboard')

@section('content')
<div class="w-full px-6 py-6 mx-auto">
    <div class="flex flex-wrap -mx-3">
        <div class="w-full max-w-full px-3 mb-6">
            <div class="relative flex flex-col min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                <div class="p-4 pb-0 mb-0 bg-white border-b-0 rounded-t-2xl">
                    <div class="flex flex-wrap -mx-3">
                        <div class="flex items-center w-full max-w-full px-3 md:w-8/12">
                            <h6 class="mb-0">Gestion des médecins</h6>
                            <p class="text-sm text-slate-500 ml-2">Liste des médecins et leurs spécialités</p>
                        </div>
                        <div class="w-full max-w-full px-3 text-right md:w-4/12">
                            <a href="{{ route('doctors.create') }}" 
                               class="inline-block px-6 py-3 mb-0 ml-6 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md">
                                + Ajouter un médecin
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

                    @if($doctors->count() > 0)
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Médecin</th>
                                        <th scope="col" class="px-6 py-3">Spécialité</th>
                                        <th scope="col" class="px-6 py-3">Contact</th>
                                        <th scope="col" class="px-6 py-3">Statut</th>
                                        <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($doctors as $doctor)
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full" src="{{ $doctor->profile_photo_url }}" alt="Photo de profil">
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="font-medium text-gray-900">
                                                            {{ $doctor->full_name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $doctor->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($doctor->doctorProfile && $doctor->doctorProfile->specialty)
                                                    <div class="text-sm font-medium text-gray-900">
                                                    Spécialité:    {{ $doctor->doctorProfile->specialty->name }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                    Département:    {{ $doctor->doctorProfile->specialty->department->name ?? '' }}
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500">Non spécifiée</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $doctor->phone }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $doctor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $doctor->is_active ? 'Actif' : 'Inactif' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex items-center justify-end space-x-3">
                                                    <a href="{{ route('doctors.show', $doctor) }}" 
                                                       class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                                        <i class="fas fa-eye"></i> Voir
                                                    </a>
                                                    <a href="{{ route('doctors.edit', $doctor) }}" 
                                                       class="text-sm font-medium text-cyan-600 hover:text-cyan-800">
                                                        <i class="fas fa-edit"></i> Modifier
                                                    </a>
                                                    <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" class="inline" 
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce médecin ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-800">
                                                            <i class="fas fa-trash"></i> Supprimer
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $doctors->links() }}
                        </div>
                    @else
                        <div class="p-6 text-center">
                            <p class="text-gray-500">Aucun médecin enregistré pour le moment.</p>
                            <a href="{{ route('doctors.create') }}" 
                               class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Ajouter votre premier médecin
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        {{ $doctors->links() }}
    </div>
</div>
@endsection
