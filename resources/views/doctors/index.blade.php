@extends('layouts.app-dashboard')

@section('content')
<div class="w-full px-6 py-6 mx-auto">
    <div class="flex flex-wrap -mx-3">
        <div class="w-full max-w-full px-3 mb-6">
            <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-slate-800 border-0 shadow-soft-xl rounded-2xl bg-clip-border text-slate-700 dark:text-slate-100">
                <div class="p-4 pb-0 mb-0 bg-white dark:bg-slate-800 border-b-0 rounded-t-2xl">
                    <div class="flex flex-wrap -mx-3">
                        <div class="flex items-center w-full max-w-full px-3 md:w-8/12">
                            <h6 class="mb-0">Gestion des médecins</h6>
                            <p class="text-sm text-slate-500 dark:text-slate-300 ml-2">Liste des médecins et leurs spécialités</p>
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
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 dark:bg-green-900/40 dark:text-green-300 rounded-lg" role="alert">
                            <span class="font-medium">Succès !</span> {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 dark:bg-red-900/40 dark:text-red-300 rounded-lg" role="alert">
                            <span class="font-medium">Erreur !</span> {{ session('error') }}
                        </div>
                    @endif

                    @if($doctors->count() > 0)
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-slate-300">
                                <thead class="text-xs text-gray-700 dark:text-slate-200 uppercase bg-gray-50 dark:bg-slate-900">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Médecin</th>
                                        <th scope="col" class="px-6 py-3">Affiliations</th>
                                        <th scope="col" class="px-6 py-3">Contact</th>
                                        <th scope="col" class="px-6 py-3">Statut</th>
                                        <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                    @foreach($doctors as $doctor)
                                        @php
                                            $doctor->load('doctorProfiles.specialty.department');
                                        @endphp
                                        <tr class="bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full" src="{{ $doctor->profile_photo_url }}" alt="Photo de profil">
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-slate-100">
                                                            {{ $doctor->full_name }}
                                                        </div>
                                                        <div class="text-sm text-gray-500 dark:text-slate-300">
                                                            {{ $doctor->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($doctor->doctorProfiles->count() > 0)
                                                    <div class="space-y-1">
                                                        @foreach($doctor->doctorProfiles->take(2) as $profile)
                                                            <div class="text-sm text-gray-900 dark:text-slate-100">
                                                                <span class="font-medium">{{ $profile->specialty->name ?? 'Non spécifié' }}</span>
                                                                <span class="text-gray-500 dark:text-slate-300 text-xs">({{ $profile->specialty->department->name ?? 'Non spécifié' }})</span>
                                                            </div>
                                                        @endforeach
                                                        @if($doctor->doctorProfiles->count() > 2)
                                                            <div class="text-xs text-gray-500 dark:text-slate-400">
                                                                + {{ $doctor->doctorProfiles->count() - 2 }} autre(s)
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-400 dark:text-slate-400">Aucune affiliation</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 dark:text-slate-100">{{ $doctor->phone }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $doctor->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/60 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-200' }}">
                                                    {{ $doctor->is_active ? 'Actif' : 'Inactif' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex justify-end space-x-2">
                                                    <button onclick="toggleDoctorStatus({{ $doctor->id }}, {{ $doctor->is_active ? 'true' : 'false' }})" 
                                                            class="{{ $doctor->is_active ? 'inline-block px-6 py-3 mb-0 ml-6 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md' : 'bg-green-100 text-green-600 hover:bg-green-200' }} px-3 py-1 rounded-full text-xs font-medium transition-colors">
                                                        {{ $doctor->is_active ? 'Désactiver' : 'Activer' }}
                                                    </button>
                                                    <a href="{{ route('doctors.show', $doctor) }}" class="inline-block px-6 py-3 mb-0 ml-6 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('doctors.edit', $doctor) }}" class="inline-block px-6 py-3 mb-0 ml-6 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                            {{ $doctors->links() }}
                        </div>
                    @else
                        <div class="p-6 text-center">
                            <p class="text-gray-500 dark:text-slate-300">Aucun médecin enregistré pour le moment.</p>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function toggleDoctorStatus(doctorId, isCurrentlyActive) {
    const action = isCurrentlyActive ? 'désactiver' : 'activer';
    
    Swal.fire({
        title: 'Confirmer',
        text: `Voulez-vous vraiment ${action} ce médecin ?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui, ' + action,
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            // Envoyer la requête AJAX
            fetch(`/doctors/${doctorId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'POST'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger la page pour voir les changements
                    Swal.fire(
                        'Succès !',
                        data.message,
                        'success'
                    ).then(() => {
                        window.location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Une erreur est survenue');
                }
            })
            .catch(error => {
                Swal.fire(
                    'Erreur !',
                    error.message || 'Une erreur est survenue lors de la mise à jour du statut',
                    'error'
                );
            });
        }
    });
}
</script>
@endpush
