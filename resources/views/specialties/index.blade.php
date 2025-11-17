@extends('layouts.app-dashboard')

@section('content')
<div class="w-full px-6 py-6 mx-auto">
    <div class="flex flex-wrap -mx-3">
        <div class="w-full max-w-full px-3 mb-6">
            <div class="relative flex flex-col min-w-0 break-words bg-white dark:bg-slate-800 border-0 shadow-soft-xl rounded-2xl bg-clip-border text-slate-700 dark:text-slate-100">
                <div class="p-4 pb-0 mb-0 bg-white dark:bg-slate-800 border-b-0 rounded-t-2xl">
                    <div class="flex flex-wrap -mx-3">
                        <div class="flex items-center w-full max-w-full px-3 md:w-8/12">
                            <h6 class="mb-0">Gestion des spécialités</h6>
                            @if(request()->has('department'))
                                <p class="text-sm text-slate-500 dark:text-slate-300 ml-2">
                                    Département : {{ $departments->find(request('department'))->name }}
                                </p>
                            @endif
                        </div>
                        <div class="w-full max-w-full px-3 text-right md:w-4/12">
                            <a href="{{ route('departments.index') }}" 
                               class="inline-block px-6 py-3 mb-0 ml-6 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md">
                                Retour
                            </a>
                            <a href="{{ route('specialties.create', ['department' => request('department')]) }}" 
                               class="inline-block px-6 py-3 mb-0 ml-6 font-bold text-center text-white uppercase align-middle transition-all border-0 rounded-lg cursor-pointer hover:scale-102 active:opacity-85 hover:shadow-soft-xs bg-gradient-to-tl from-purple-700 to-pink-500 leading-pro text-xs ease-soft-in tracking-tight-soft shadow-soft-md">
                                + Ajouter une spécialité
                            </a>
                        </div>
                    </div>
                    <div class="mt-4">
                        <form method="GET" action="{{ route('specialties.index') }}" class="max-w-xs">
                            <select name="department" onchange="this.form.submit()" 
                                class="block w-full text-sm rounded-md border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Tous les départements</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
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

                    @if($specialties->count() > 0)
                        <div class="relative overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-slate-300">
                                <thead class="text-xs text-gray-700 dark:text-slate-200 uppercase bg-gray-50 dark:bg-slate-900">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Nom</th>
                                        <th scope="col" class="px-6 py-3">Département</th>
                                        <th scope="col" class="px-6 py-3">Description</th>
                                        <th scope="col" class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                                    @foreach($specialties as $specialty)
                                        <tr class="bg-white dark:bg-slate-800 border-b border-gray-100 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-700">
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-slate-100 whitespace-nowrap">
                                                {{ $specialty->name }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200">
                                                    {{ $specialty->department->name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-600 dark:text-slate-300">
                                                    {{ Str::limit($specialty->description, 50) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <div class="flex items-center justify-end space-x-3">
                                                    <a href="{{ route('specialties.edit', $specialty) }}" 
                                                       class="text-sm font-medium text-cyan-600 hover:text-cyan-800">
                                                        <i class="fas fa-edit"></i> Modifier
                                                    </a>
                                                    <form action="{{ route('specialties.destroy', $specialty) }}" method="POST" class="inline" 
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette spécialité ?')">
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
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-slate-700">
                            {{ $specialties->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="p-6 text-center">
                            <p class="text-gray-500 dark:text-slate-300">Aucune spécialité enregistrée pour le moment.</p>
                            <a href="{{ route('specialties.create', ['department' => request('department')]) }}" 
                               class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Ajouter votre première spécialité
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection