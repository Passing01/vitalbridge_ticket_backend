@extends('layouts.guest')

@section('content')
<main class="mt-0 transition-all duration-200 ease-soft-in-out">
    <section class="min-h-screen mb-32">
        <div class="relative flex items-start pt-12 pb-56 m-4 overflow-hidden bg-center bg-cover min-h-50-screen rounded-xl" style="background-image: url('{{ asset('assets/img/curved-images/curved14.jpg') }}')">
            <span class="absolute top-0 left-0 w-full h-full bg-center bg-cover bg-gradient-to-tl from-gray-900 to-slate-800 opacity-80"></span>
            <div class="container z-10">
                <div class="flex flex-wrap justify-center -mx-3">
                    <div class="w-full max-w-full px-3 mx-auto mt-0 text-center lg:flex-0 shrink-0 lg:w-6/12">
                        <h1 class="mt-12 mb-2 text-white text-3xl font-bold">Activation de votre compte réception</h1>
                        <p class="text-white text-sm max-w-xl mx-auto">
                            Avant d'accéder au tableau de bord, veuillez compléter ce paiement simulé. Aucun débit réel ne sera effectué, il s'agit uniquement d'une étape de simulation.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="flex flex-wrap justify-center -mx-3 mt-[-7rem]">
                <div class="w-full max-w-full px-3 mb-6 lg:mb-0 lg:w-8/12">
                    <div class="relative flex flex-col min-w-0 break-words bg-white shadow-soft-xl rounded-2xl bg-clip-border">
                        <div class="flex-auto p-6">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h6 class="mb-1 font-bold">Paiement d'inscription (simulé)</h6>
                                    <p class="mb-0 text-sm text-slate-500">Remplissez ce formulaire pour activer votre compte réception.</p>
                                </div>
                            </div>

                            @if(session('status'))
                                <div class="mb-4 rounded-lg bg-emerald-100 px-4 py-3 text-sm text-emerald-800">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form action="{{ route('billing.simulate.submit') }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">Nom sur la carte</label>
                                        <input type="text" name="card_name" value="{{ auth()->user()->full_name ?? '' }}" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-400" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">Numéro de carte</label>
                                        <input type="text" name="card_number" maxlength="19" placeholder="4242 4242 4242 4242" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-400" required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">Date d'expiration</label>
                                        <input type="text" name="card_exp" placeholder="MM/YY" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-400" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">CVC</label>
                                        <input type="text" name="card_cvc" maxlength="4" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-400" required>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">Montant</label>
                                        <input type="text" name="amount" value="10000" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-fuchsia-400" readonly>
                                    </div>
                                </div>

                                <div class="flex justify-end mt-4">
                                    <button type="submit" class="px-4 py-2 text-sm font-semibold text-white bg-gradient-to-tl from-purple-700 to-pink-500 rounded-lg shadow-soft-2xl">
                                        Simuler le paiement
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="w-full max-w-full px-3 lg:w-4/12">
                    <div class="relative flex flex-col h-full min-w-0 break-words bg-white border-0 shadow-soft-xl rounded-2xl bg-clip-border">
                        <div class="p-4 pb-0 mb-0 border-b-0 rounded-t-2xl border-b-solid border-b-transparent">
                            <h6 class="mb-0">Récapitulatif</h6>
                        </div>
                        <div class="flex-auto p-4 pt-2">
                            <ul class="flex flex-col pl-0 mb-0 rounded-lg">
                                <li class="relative flex justify-between px-4 py-2 mb-2 border-0 rounded-t-lg text-sm text-slate-700 bg-slate-50">
                                    <span class="font-semibold">Type de compte</span>
                                    <span>Réception</span>
                                </li>
                                <li class="relative flex justify-between px-4 py-2 mb-2 border-0 text-sm text-slate-700">
                                    <span class="font-semibold">Centre</span>
                                    <span>{{ auth()->user()->full_name ?? auth()->user()->email }}</span>
                                </li>
                                <li class="relative flex justify-between px-4 py-2 mb-2 border-0 text-sm text-slate-700">
                                    <span class="font-semibold">Montant</span>
                                    <span>10 000 FCFA</span>
                                </li>
                                <li class="relative flex justify-between px-4 py-2 mb-2 border-0 rounded-b-lg text-sm text-slate-700 bg-slate-50">
                                    <span class="font-semibold">Statut</span>
                                    <span>
                                        @if(session('status'))
                                            <span class="text-emerald-600 font-semibold">Payé (simulé)</span>
                                        @else
                                            <span class="text-orange-500 font-semibold">En attente de paiement</span>
                                        @endif
                                    </span>
                                </li>
                            </ul>

                            <p class="mt-4 text-xs text-slate-400">
                                Ceci est une simulation de paiement, aucune transaction réelle n'est effectuée.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
