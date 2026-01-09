@extends('layouts.qmatic')

@section('title', 'Paramètres Qmatic')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Paramètres de Personnalisation</h1>
        <p class="text-gray-600">Personnalisez l'apparence de votre borne et de votre écran public.</p>
    </div>

    <form action="{{ route('qmatic.admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="grid grid-cols-1 gap-6">
            <!-- Identité de la structure -->
            <div class="qmatic-card p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Identité de la Structure</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="structure_name" class="block text-sm font-medium text-gray-700">Nom de l'établissement</label>
                        <input type="text" name="structure_name" id="structure_name" value="{{ $settings['structure_name'] }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="structure_logo" class="block text-sm font-medium text-gray-700">Logo de l'établissement</label>
                        @if($settings['structure_logo'])
                            <div class="mb-2">
                                <img src="{{ $settings['structure_logo'] }}" alt="Logo" class="h-12 w-auto object-contain bg-gray-100 p-1 rounded">
                            </div>
                        @endif
                        <input type="file" name="structure_logo" id="structure_logo" accept="image/*"
                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                </div>
            </div>

            <!-- Apparence et Couleurs -->
            <div class="qmatic-card p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Apparence et Couleurs</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700">Couleur principale</label>
                        <div class="mt-1 flex items-center gap-3">
                            <input type="color" name="primary_color" id="primary_color" value="{{ $settings['primary_color'] }}"
                                class="h-10 w-20 p-1 rounded border border-gray-300">
                            <span class="text-sm text-gray-500">{{ $settings['primary_color'] }}</span>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Utilisée pour les boutons et les accents sur la borne.</p>
                    </div>
                </div>
            </div>

            <!-- Messages et Textes -->
            <div class="qmatic-card p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Messages et Textes</h2>
                <div class="space-y-4">
                    <div>
                        <label for="welcome_message" class="block text-sm font-medium text-gray-700">Message de bienvenue (Borne)</label>
                        <input type="text" name="welcome_message" id="welcome_message" value="{{ $settings['welcome_message'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                    
                    <div>
                        <label for="display_announcement" class="block text-sm font-medium text-gray-700">Message d'annonce (Écran - Texte défilant)</label>
                        <input type="text" name="display_announcement" id="display_announcement" value="{{ $settings['display_announcement'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="announcement_language" class="block text-sm font-medium text-gray-700">Langue principale</label>
                            <select name="announcement_language" id="announcement_language" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="fr-FR" {{ $settings['announcement_language'] == 'fr-FR' ? 'selected' : '' }}>Français</option>
                                <option value="en-US" {{ $settings['announcement_language'] == 'en-US' ? 'selected' : '' }}>Anglais</option>
                            </select>
                        </div>

                        <div>
                            <label for="announcement_gender" class="block text-sm font-medium text-gray-700">Type de voix</label>
                            <select name="announcement_gender" id="announcement_gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="female" {{ $settings['announcement_gender'] == 'female' ? 'selected' : '' }}>Femme</option>
                                <option value="male" {{ $settings['announcement_gender'] == 'male' ? 'selected' : '' }}>Homme</option>
                            </select>
                        </div>

                        <div class="flex items-center pt-6">
                            <input type="hidden" name="announcement_multi_lang" value="0">
                            <input type="checkbox" name="announcement_multi_lang" id="announcement_multi_lang" value="1" {{ $settings['announcement_multi_lang'] ? 'checked' : '' }}
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="announcement_multi_lang" class="ml-2 block text-sm text-gray-900">
                                Activer l'annonce multi-langue (Français + Local)
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="announcement_template" class="block text-sm font-medium text-gray-700">Modèle d'annonce (Français)</label>
                        <input type="text" name="announcement_template" id="announcement_template" value="{{ $settings['announcement_template'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Ex: "Ticket {ticket}, au guichet {counter}"</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Couleurs Écran Public</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Fond</label>
                                    <input type="color" name="display_bg_color" value="{{ $settings['display_bg_color'] }}" class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Secondaire</label>
                                    <input type="color" name="display_secondary_color" value="{{ $settings['display_secondary_color'] }}" class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Texte</label>
                                    <input type="color" name="display_text_color" value="{{ $settings['display_text_color'] }}" class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                            <div>
                                <label for="display_layout" class="block text-sm font-medium text-gray-700">Disposition de l'Écran</label>
                                <select name="display_layout" id="display_layout" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="sidebar_right" {{ $settings['display_layout'] == 'sidebar_right' ? 'selected' : '' }}>Standard (File à droite)</option>
                                    <option value="sidebar_left" {{ $settings['display_layout'] == 'sidebar_left' ? 'selected' : '' }}>Inversé (File à gauche)</option>
                                    <option value="compact_bottom" {{ $settings['display_layout'] == 'compact_bottom' ? 'selected' : '' }}>Compact (File en bas - Idéal Vidéos)</option>
                                </select>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-700 uppercase tracking-wider">Couleurs & Layout Borne</h4>
                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Fond</label>
                                    <input type="color" name="kiosk_bg_color" value="{{ $settings['kiosk_bg_color'] }}" class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Cartes</label>
                                    <input type="color" name="kiosk_card_bg_color" value="{{ $settings['kiosk_card_bg_color'] }}" class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500">Texte</label>
                                    <input type="color" name="kiosk_text_color" value="{{ $settings['kiosk_text_color'] }}" class="mt-1 block w-full h-10 rounded-md border-gray-300 shadow-sm">
                                </div>
                            </div>
                            <div>
                                <label for="kiosk_layout" class="block text-sm font-medium text-gray-700">Disposition des Services</label>
                                <select name="kiosk_layout" id="kiosk_layout" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    <option value="grid" {{ $settings['kiosk_layout'] == 'grid' ? 'selected' : '' }}>Grille (Cards)</option>
                                    <option value="list" {{ $settings['kiosk_layout'] == 'list' ? 'selected' : '' }}>Liste (Ligne par ligne)</option>
                                    <option value="large_cards" {{ $settings['kiosk_layout'] == 'large_cards' ? 'selected' : '' }}>Grandes Cartes</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div id="local-languages-manager" class="space-y-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <div class="flex justify-between items-center">
                            <h4 class="text-sm font-bold text-blue-800 uppercase tracking-wider">Gestion des Langues Locales</h4>
                            <button type="button" id="add-language" class="text-xs bg-blue-600 text-white px-2 py-1 rounded hover:bg-blue-700">
                                + Ajouter une langue
                            </button>
                        </div>
                        
                        <div id="languages-list" class="space-y-3">
                            <!-- Les langues seront ajoutées ici dynamiquement -->
                        </div>
                        
                        <input type="hidden" name="local_languages" id="local_languages_input" value="{{ $settings['local_languages'] }}">
                    </div>

                    <script>
                        const languagesList = document.getElementById('languages-list');
                        const languagesInput = document.getElementById('local_languages_input');
                        let languages = JSON.parse(languagesInput.value || '[]');

                        function renderLanguages() {
                            languagesList.innerHTML = '';
                            languages.forEach((lang, index) => {
                                const div = document.createElement('div');
                                div.className = 'bg-white p-3 rounded-lg shadow-sm border border-blue-100 grid grid-cols-1 md:grid-cols-3 gap-3 items-end';
                                div.innerHTML = `
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500">Nom de la langue</label>
                                        <input type="text" value="${lang.name}" onchange="updateLang(${index}, 'name', this.value)" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500">Modèle d'annonce</label>
                                        <input type="text" value="${lang.template}" onchange="updateLang(${index}, 'template', this.value)" class="mt-1 block w-full rounded-md border-gray-300 text-sm">
                                    </div>
                                    <div class="flex gap-2">
                                        <div class="flex-grow">
                                            <label class="block text-xs font-medium text-gray-500">Audio (Optionnel)</label>
                                            <input type="file" accept="audio/*" onchange="handleAudioUpload(${index}, this)" class="mt-1 block w-full text-xs">
                                        </div>
                                        <button type="button" onclick="removeLang(${index})" class="text-red-500 hover:text-red-700 p-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                `;
                                languagesList.appendChild(div);
                            });
                            languagesInput.value = JSON.stringify(languages);
                        }

                        function updateLang(index, field, value) {
                            languages[index][field] = value;
                            languagesInput.value = JSON.stringify(languages);
                        }

                        function removeLang(index) {
                            languages.splice(index, 1);
                            renderLanguages();
                        }

                        document.getElementById('add-language').addEventListener('click', () => {
                            languages.push({ name: '', template: 'Ticket {ticket}, guichet {counter}', audio: null });
                            renderLanguages();
                        });

                        async function handleAudioUpload(index, input) {
                            if (!input.files || !input.files[0]) return;
                            const formData = new FormData();
                            formData.append('audio', input.files[0]);
                            formData.append('_token', '{{ csrf_token() }}');

                            // Note: On pourrait créer une route dédiée pour l'upload d'audio
                            // Pour l'instant on stocke le nom ou on simule
                            languages[index].audio_name = input.files[0].name;
                            languagesInput.value = JSON.stringify(languages);
                        }

                        renderLanguages();
                    </script>

                    <script>
                        document.getElementById('announcement_multi_lang').addEventListener('change', function() {
                            document.getElementById('local-lang-templates').classList.toggle('hidden', !this.checked);
                        });
                    </script>

                    <div>
                        <label for="ticket_footer" class="block text-sm font-medium text-gray-700">Pied de page du ticket</label>
                        <input type="text" name="ticket_footer" id="ticket_footer" value="{{ $settings['ticket_footer'] }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-qmatic px-8 py-3 text-lg">
                    Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
