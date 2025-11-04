<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VitalBridge - Plateforme de gestion de tickets</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            /* Styles de base */
            body {
                font-family: 'Instrument Sans', sans-serif;
                background-color: #f8fafc;
                color: #1e293b;
                line-height: 1.5;
            }
            
            .btn {
                display: inline-block;
                font-weight: 600;
                text-align: center;
                white-space: nowrap;
                vertical-align: middle;
                user-select: none;
                border: 1px solid transparent;
                padding: 0.5rem 1.5rem;
                font-size: 1rem;
                line-height: 1.5;
                border-radius: 0.375rem;
                transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }
            
            .btn-primary {
                color: #fff;
                background-color: #3b82f6;
                border-color: #3b82f6;
            }
            
            .btn-primary:hover {
                background-color: #2563eb;
                border-color: #1d4ed8;
            }
            
            .btn-secondary {
                color: #1e293b;
                background-color: #e2e8f0;
                border-color: #cbd5e1;
            }
            
            .btn-secondary:hover {
                background-color: #cbd5e1;
                border-color: #94a3b8;
            }
            
            .container {
                width: 100%;
                margin-right: auto;
                margin-left: auto;
                padding-right: 1rem;
                padding-left: 1rem;
            }
            
            @media (min-width: 640px) {
                .container {
                    max-width: 640px;
                }
            }
            
            @media (min-width: 768px) {
                .container {
                    max-width: 768px;
                }
            }
            
            @media (min-width: 1024px) {
                .container {
                    max-width: 1024px;
                }
            }
            
            @media (min-width: 1280px) {
                .container {
                    max-width: 1280px;
                }
            }
            
            .text-center {
                text-align: center;
            }
            
            .py-16 {
                padding-top: 4rem;
                padding-bottom: 4rem;
            }
            
            .mt-8 {
                margin-top: 2rem;
            }
            
            .space-x-4 > :not([hidden]) ~ :not([hidden]) {
                margin-right: 1rem;
                margin-left: 0;
            }
            
            .flex {
                display: flex;
            }
            
            .items-center {
                align-items: center;
            }
            
            .justify-between {
                justify-content: space-between;
            }
            
            .hidden {
                display: none;
            }
            
            @media (min-width: 768px) {
                .md\:flex {
                    display: flex;
                }
                
                .md\:hidden {
                    display: none;
                }
                
                .md\:space-x-8 > :not([hidden]) ~ :not([hidden]) {
                    margin-right: 2rem;
                    margin-left: 0;
                }
            }
            
            .text-4xl {
                font-size: 2.25rem;
                line-height: 2.5rem;
            }
            
            .font-bold {
                font-weight: 700;
            }
            
            .text-gray-900 {
                color: #111827;
            }
            
            .text-gray-600 {
                color: #4b5563;
            }
            
            .bg-white {
                background-color: #fff;
            }
            
            .shadow-sm {
                box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            }
            
            .px-4 {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .py-3 {
                padding-top: 0.75rem;
                padding-bottom: 0.75rem;
            }
            
            .mt-12 {
                margin-top: 3rem;
            }
            
            .grid {
                display: grid;
            }
            
            .grid-cols-1 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
            
            .gap-8 {
                gap: 2rem;
            }
            
            .rounded-lg {
                border-radius: 0.5rem;
            }
            
            .p-6 {
                padding: 1.5rem;
            }
            
            .bg-gray-50 {
                background-color: #f9fafb;
            }
            
            .text-2xl {
                font-size: 1.5rem;
                line-height: 2rem;
            }
            
            .mt-4 {
                margin-top: 1rem;
            }
            
            .text-sm {
                font-size: 0.875rem;
                line-height: 1.25rem;
            }
            
            .text-blue-600 {
                color: #2563eb;
            }
            
            .hover\:text-blue-700:hover {
                color: #1d4ed8;
            }
            
            .mt-6 {
                margin-top: 1.5rem;
            }
            
            .w-full {
                width: 100%;
            }
            
            @media (min-width: 768px) {
                .md\:grid-cols-3 {
                    grid-template-columns: repeat(3, minmax(0, 1fr));
                }
                
                .md\:text-5xl {
                    font-size: 3rem;
                    line-height: 1;
                }
                
                .md\:text-lg {
                    font-size: 1.125rem;
                    line-height: 1.75rem;
                }
            }
            
            .h-8 {
                height: 2rem;
            }
            
            .text-gray-400 {
                color: #9ca3af;
            }
            
            .mt-2 {
                margin-top: 0.5rem;
            }
            
            .text-gray-700 {
                color: #374151;
            }
            
            .text-xs {
                font-size: 0.75rem;
                line-height: 1rem;
            }
            
            .uppercase {
                text-transform: uppercase;
            }
            
            .tracking-wider {
                letter-spacing: 0.05em;
            }
            
            .font-medium {
                font-weight: 500;
            }
            
            .mt-10 {
                margin-top: 2.5rem;
            }
            
            .max-w-7xl {
                max-width: 80rem;
            }
            
            .mx-auto {
                margin-left: auto;
                margin-right: auto;
            }
            
            .px-6 {
                padding-left: 1.5rem;
                padding-right: 1.5rem;
            }
            
            .lg\:px-8 {
                padding-left: 2rem;
                padding-right: 2rem;
            }
            
            .lg\:flex-row {
                flex-direction: row;
            }
            
            .flex-col {
                flex-direction: column;
            }
            
            .lg\:pt-24 {
                padding-top: 6rem;
            }
            
            .lg\:pb-24 {
                padding-bottom: 6rem;
            }
            
            .lg\:text-left {
                text-align: left;
            }
            
            .lg\:mt-0 {
                margin-top: 0;
            }
            
            .lg\:ml-10 {
                margin-left: 2.5rem;
            }
            
            .flex-1 {
                flex: 1 1 0%;
            }
            
            .lg\:max-w-lg {
                max-width: 32rem;
            }
            
            .text-3xl {
                font-size: 1.875rem;
                line-height: 2.25rem;
            }
            
            .leading-9 {
                line-height: 2.25rem;
            }
            
            .mt-3 {
                margin-top: 0.75rem;
            }
            
            .text-xl {
                font-size: 1.25rem;
                line-height: 1.75rem;
            }
            
            .text-gray-500 {
                color: #6b7280;
            }
            
            .mt-5 {
                margin-top: 1.25rem;
            }
            
            .sm\:flex-shrink-0 {
                flex-shrink: 0;
            }
            
            .sm\:w-16 {
                width: 4rem;
            }
            
            .sm\:h-16 {
                height: 4rem;
            }
            
            .text-blue-500 {
                color: #3b82f6;
            }
            
            .bg-blue-100 {
                background-color: #dbeafe;
            }
            
            .rounded-md {
                border-radius: 0.375rem;
            }
            
            .flex {
                display: flex;
            }
            
            .items-center {
                align-items: center;
            }
            
            .justify-center {
                justify-content: center;
            }
            
            .h-12 {
                height: 3rem;
            }
            
            .w-12 {
                width: 3rem;
            }
            
            .text-2xl {
                font-size: 1.5rem;
                line-height: 2rem;
            }
            
            .ml-4 {
                margin-left: 1rem;
            }
            
            .text-base {
                font-size: 1rem;
                line-height: 1.5rem;
            }
            
            .font-medium {
                font-weight: 500;
            }
            
            .text-gray-900 {
                color: #111827;
            }
            
            .hover\:text-gray-600:hover {
                color: #4b5563;
            }
            
            .mt-1 {
                margin-top: 0.25rem;
            }
            
            .text-gray-500 {
                color: #6b7280;
            }
            
            .mt-6 {
                margin-top: 1.5rem;
            }
            
            .border-t {
                border-top-width: 1px;
            }
            
            .border-gray-200 {
                border-color: #e5e7eb;
            }
            
            .pt-6 {
                padding-top: 1.5rem;
            }
            
            .md\:flex {
                display: flex;
            }
            
            .md\:flex-1 {
                flex: 1 1 0%;
            }
            
            .md\:grid-cols-3 {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
            
            .md\:gap-8 {
                gap: 2rem;
            }
            
            .md\:border-t-0 {
                border-top-width: 0;
            }
            
            .md\:border-l {
                border-left-width: 1px;
            }
            
            .md\:border-gray-200 {
                border-color: #e5e7eb;
            }
            
            .md\:pt-0 {
                padding-top: 0;
            }
            
            .md\:pl-6 {
                padding-left: 1.5rem;
            }
            
            .md\:text-right {
                text-align: right;
            }
            
            .md\:border-l-0 {
                border-left-width: 0;
            }
            
            .md\:border-t {
                border-top-width: 1px;
            }
            
            .md\:py-5 {
                padding-top: 1.25rem;
                padding-bottom: 1.25rem;
            }
            
            .md\:pr-6 {
                padding-right: 1.5rem;
            }
            
            .md\:pl-6 {
                padding-left: 1.5rem;
            }
        </style>
    @endif
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <svg class="h-8 w-auto text-blue-600" viewBox="0 0 40 40" fill="currentColor">
                                <path fill-rule="evenodd" d="M20 40C8.954 40 0 31.046 0 20S8.954 0 20 0s20 8.954 20 20-8.954 20-20 20zm1-30a1 1 0 00-2 0v9H10a1 1 0 100 2h9v9a1 1 0 102 0v-9h9a1 1 0 100-2h-9v-9z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-2 text-xl font-bold text-gray-900">VitalBridge</span>
                        </div>
                        <div class="hidden md:ml-10 md:flex md:space-x-8">
                            <a href="#" class="border-blue-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Accueil</a>
                            <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Fonctionnalités</a>
                            <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Tarifs</a>
                            <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Contact</a>
                        </div>
                    </div>
                    <div class="hidden md:ml-4 md:flex md:items-center md:space-x-4">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium">Tableau de bord</a>
                            @else
                                <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium">Connexion</a>
                                <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">S'inscrire</a>
                            @endauth
                        @endif
                    </div>
                    <div class="-mr-2 flex items-center md:hidden">
                        <!-- Mobile menu button -->
                        <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" id="mobile-menu-button">
                            <span class="sr-only">Ouvrir le menu principal</span>
                            <!-- Icone du menu hamburger -->
                            <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Menu mobile -->
            <div class="md:hidden hidden" id="mobile-menu">
                <div class="pt-2 pb-3 space-y-1">
                    <a href="#" class="bg-blue-50 border-blue-500 text-blue-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Accueil</a>
                    <a href="#" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Fonctionnalités</a>
                    <a href="#" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Tarifs</a>
                    <a href="#" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Contact</a>
                </div>
                <div class="pt-4 pb-3 border-t border-gray-200">
                    <div class="mt-3 space-y-1">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Tableau de bord</a>
                            @else
                                <a href="{{ route('login') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Connexion</a>
                                <a href="{{ route('register') }}" class="block px-4 py-2 text-base font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50">S'inscrire</a>
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="bg-white">
            <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl">
                    <span class="block">Gérez vos tickets</span>
                    <span class="block text-blue-600">en toute simplicité</span>
                </h1>
                <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                    VitalBridge est la solution tout-en-un pour gérer efficacement vos demandes de support et améliorer la satisfaction client.
                </p>
                <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                    <div class="rounded-md shadow">
                        <a href="{{ route('register') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10">
                            Commencer gratuitement
                        </a>
                    </div>
                    <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                        <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                            Voir la démo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:text-center">
                    <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Fonctionnalités</h2>
                    <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        Une meilleure façon de gérer les tickets
                    </p>
                    <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                        Découvrez comment VitalBridge peut transformer votre service client
                    </p>
                </div>

                <div class="mt-10">
                    <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
                        <!-- Feature 1 -->
                        <div class="relative">
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-16">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Gestion simplifiée</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    Créez, suivez et gérez facilement tous vos tickets en un seul endroit.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 2 -->
                        <div class="relative">
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-16">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Temps de réponse rapide</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    Réduisez les temps de réponse et améliorez la satisfaction client.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 3 -->
                        <div class="relative">
                            <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="ml-16">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Rapports détaillés</h3>
                                <p class="mt-2 text-base text-gray-500">
                                    Obtenez des insights précieux sur les performances de votre support.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-blue-700">
            <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                    <span class="block">Prêt à commencer ?</span>
                    <span class="block">Créez votre compte dès aujourd'hui.</span>
                </h2>
                <p class="mt-4 text-lg leading-6 text-blue-200">
                    Essai gratuit de 14 jours. Aucune carte de crédit requise. Annulez à tout moment.
                </p>
                <a href="{{ route('register') }}" class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 sm:w-auto">
                    S'inscrire maintenant
                </a>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white">
            <div class="max-w-7xl mx-auto py-12 px-4 overflow-hidden sm:px-6 lg:px-8">
                <nav class="-mx-5 -my-2 flex flex-wrap justify-center" aria-label="Footer">
                    <div class="px-5 py-2">
                        <a href="#" class="text-base text-gray-500 hover:text-gray-900">À propos</a>
                    </div>
                    <div class="px-5 py-2">
                        <a href="#" class="text-base text-gray-500 hover:text-gray-900">Blog</a>
                    </div>
                    <div class="px-5 py-2">
                        <a href="#" class="text-base text-gray-500 hover:text-gray-900">Emplois</a>
                    </div>
                    <div class="px-5 py-2">
                        <a href="#" class="text-base text-gray-500 hover:text-gray-900">Presse</a>
                    </div>
                    <div class="px-5 py-2">
                        <a href="#" class="text-base text-gray-500 hover:text-gray-900">Accessibilité</a>
                    </div>
                    <div class="px-5 py-2">
                        <a href="#" class="text-base text-gray-500 hover:text-gray-900">Partenaires</a>
                    </div>
                </nav>
                <div class="mt-8 flex justify-center space-x-6">
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">Twitter</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-gray-500">
                        <span class="sr-only">GitHub</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.699 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2s-10 4.484-10 10 4.477 10 10 10 10-4.484 10-10z" />
                        </svg>
                    </a>
                </div>
                <p class="mt-8 text-center text-base text-gray-400">
                    &copy; {{ date('Y') }} VitalBridge. Tous droits réservés.
                </p>
            </div>
        </footer>
    </div>

    <!-- Script pour le menu mobile -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function() {
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>
            <!-- Navigation -->
            <nav class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <div class="flex-shrink-0 flex items-center">
                                <svg class="h-8 w-auto text-blue-600" viewBox="0 0 40 40" fill="currentColor">
                                    <path fill-rule="evenodd" d="M20 40C8.954 40 0 31.046 0 20S8.954 0 20 0s20 8.954 20 20-8.954 20-20 20zm1-30a1 1 0 00-2 0v9H10a1 1 0 100 2h9v9a1 1 0 102 0v-9h9a1 1 0 100-2h-9v-9z" clip-rule="evenodd" />
                                </svg>
                                <span class="ml-2 text-xl font-bold text-gray-900">VitalBridge</span>
                            </div>
                            <div class="hidden md:ml-10 md:flex md:space-x-8">
                                <a href="#" class="border-blue-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Accueil</a>
                                <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Fonctionnalités</a>
                                <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Tarifs</a>
                                <a href="#" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">Contact</a>
                            </div>
                        </div>
                        <div class="hidden md:ml-4 md:flex md:items-center md:space-x-4">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium">Tableau de bord</a>
                                @else
                                    <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium">Connexion</a>
                                    <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">S'inscrire</a>
                                @endauth
                            @endif
                        </div>
                        <div class="-mr-2 flex items-center md:hidden">
                            <!-- Mobile menu button -->
                            <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" id="mobile-menu-button">
                                <span class="sr-only">Ouvrir le menu principal</span>
                                <!-- Icone du menu hamburger -->
                                <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Menu mobile -->
                <div class="md:hidden hidden" id="mobile-menu">
                    <div class="pt-2 pb-3 space-y-1">
                        <a href="#" class="bg-blue-50 border-blue-500 text-blue-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Accueil</a>
                        <a href="#" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Fonctionnalités</a>
                        <a href="#" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Tarifs</a>
                        <a href="#" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">Contact</a>
                    </div>
                    <div class="pt-4 pb-3 border-t border-gray-200">
                        <div class="mt-3 space-y-1">
                            @if (Route::has('login'))
                                @auth
                                    <a href="{{ url('/dashboard') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Tableau de bord</a>
                                @else
                                    <a href="{{ route('login') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100">Connexion</a>
                                    <a href="{{ route('register') }}" class="block px-4 py-2 text-base font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50">S'inscrire</a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <div class="bg-white">
                <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8 text-center">
                    <h1 class="text-4xl font-bold text-gray-900 sm:text-5xl md:text-6xl">
                        <span class="block">Gérez vos tickets</span>
                        <span class="block text-blue-600">en toute simplicité</span>
                    </h1>
                    <p class="mt-3 max-w-md mx-auto text-base text-gray-500 sm:text-lg md:mt-5 md:text-xl md:max-w-3xl">
                        VitalBridge est la solution tout-en-un pour gérer efficacement vos demandes de support et améliorer la satisfaction client.
                    </p>
                    <div class="mt-5 max-w-md mx-auto sm:flex sm:justify-center md:mt-8">
                        <div class="rounded-md shadow">
                            <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10">
                                Commencer gratuitement
                            </a>
                        </div>
                        <div class="mt-3 rounded-md shadow sm:mt-0 sm:ml-3">
                            <a href="#" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-gray-50 md:py-4 md:text-lg md:px-10">
                                Voir la démo
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div class="py-12 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="lg:text-center">
                        <h2 class="text-base text-blue-600 font-semibold tracking-wide uppercase">Fonctionnalités</h2>
                        <p class="mt-2 text-3xl leading-8 font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                            Une meilleure façon de gérer les tickets
                        </p>
                        <p class="mt-4 max-w-2xl text-xl text-gray-500 lg:mx-auto">
                            Découvrez comment VitalBridge peut transformer votre service client
                        </p>
                    </div>

                    <div class="mt-10">
                        <div class="space-y-10 md:space-y-0 md:grid md:grid-cols-3 md:gap-x-8 md:gap-y-10">
                            <!-- Feature 1 -->
                            <div class="relative">
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-16">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Gestion simplifiée</h3>
                                    <p class="mt-2 text-base text-gray-500">
                                        Créez, suivez et gérez facilement tous vos tickets en un seul endroit.
                                    </p>
                                </div>
                            </div>

                            <!-- Feature 2 -->
                            <div class="relative">
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-16">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Temps de réponse rapide</h3>
                                    <p class="mt-2 text-base text-gray-500">
                                        Réduisez les temps de réponse et améliorez la satisfaction client.
                                    </p>
                                </div>
                            </div>

                            <!-- Feature 3 -->
                            <div class="relative">
                                <div class="absolute flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <div class="ml-16">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Rapports détaillés</h3>
                                    <p class="mt-2 text-base text-gray-500">
                                        Obtenez des insights précieux sur les performances de votre support.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="bg-blue-700">
                <div class="max-w-2xl mx-auto text-center py-16 px-4 sm:py-20 sm:px-6 lg:px-8">
                    <h2 class="text-3xl font-extrabold text-white sm:text-4xl">
                        <span class="block">Prêt à commencer ?</span>
                        <span class="block">Créez votre compte dès aujourd'hui.</span>
                    </h2>
                    <p class="mt-4 text-lg leading-6 text-blue-200">
                        Essai gratuit de 14 jours. Aucune carte de crédit requise. Annulez à tout moment.
                    </p>
                    <a href="{{ route('register') }}" class="mt-8 w-full inline-flex items-center justify-center px-5 py-3 border border-transparent text-base font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 sm:w-auto">
                        S'inscrire maintenant
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <footer class="bg-white">
                <div class="max-w-7xl mx-auto py-12 px-4 overflow-hidden sm:px-6 lg:px-8">
                    <nav class="-mx-5 -my-2 flex flex-wrap justify-center" aria-label="Footer">
                        <div class="px-5 py-2">
                            <a href="#" class="text-base text-gray-500 hover:text-gray-900">À propos</a>
                        </div>
                        <div class="px-5 py-2">
                            <a href="#" class="text-base text-gray-500 hover:text-gray-900">Blog</a>
                        </div>
                        <div class="px-5 py-2">
                            <a href="#" class="text-base text-gray-500 hover:text-gray-900">Emplois</a>
                        </div>
                        <div class="px-5 py-2">
                            <a href="#" class="text-base text-gray-500 hover:text-gray-900">Presse</a>
                        </div>
                        <div class="px-5 py-2">
                            <a href="#" class="text-base text-gray-500 hover:text-gray-900">Accessibilité</a>
                        </div>
                        <div class="px-5 py-2">
                            <a href="#" class="text-base text-gray-500 hover:text-gray-900">Partenaires</a>
                        </div>
                    </nav>
                    <div class="mt-8 flex justify-center space-x-6">
                        <a href="#" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Twitter</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">GitHub</span>
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.11-4.555-4.943 0-1.091.39-1.984 1.029-2.683-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.699 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.919.678 1.852 0 1.336-.012 2.415-.012 2.743 0 .267.18.578.688.48A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                    <p class="mt-8 text-center text-base text-gray-400">
                        &copy; {{ date('Y') }} VitalBridge. Tous droits réservés.
                    </p>
                </div>
            </footer>
        </div>

        <script>
            // Gestion du menu mobile
            document.addEventListener('DOMContentLoaded', function() {
                const mobileMenuButton = document.getElementById('mobile-menu-button');
                const mobileMenu = document.getElementById('mobile-menu');
                
                if (mobileMenuButton && mobileMenu) {
                    mobileMenuButton.addEventListener('click', function() {
                        mobileMenu.classList.toggle('hidden');
                    });
                }
            });
        </script>
    </body>
</html>
                        
