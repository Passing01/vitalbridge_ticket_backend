<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\Specialty;
use App\Models\DoctorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DoctorController extends Controller
{
    /**
     * Afficher la liste des médecins
     */
    public function index()
    {
        $query = User::where('role', 'doctor')
            ->with(['doctorProfiles.specialty.department']);

        if (auth()->check() && auth()->user()->role === 'reception') {
            $currentCenterId = auth()->id();

            $query->whereHas('doctorProfiles.specialty.department', function ($q) use ($currentCenterId) {
                $q->where('reception_id', $currentCenterId);
            });
        }

        $doctors = $query->paginate(10);

        return view('doctors.index', compact('doctors'));
    }

    /**
     * Afficher le formulaire de création d'un médecin
     */
    public function create()
    {
        $departments = Department::with('specialties')->get();
        return view('doctors.create', compact('departments'));
    }

    /**
     * Enregistrer un nouveau médecin ou ajouter une nouvelle affiliation
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('role', 'doctor');
                })->ignore($request->input('existing_doctor_id'))
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('role', 'doctor');
                })->ignore($request->input('existing_doctor_id'))
            ],
            'specialty_id' => 'required|exists:specialties,id',
            'qualification' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'max_patients_per_day' => 'nullable|integer|min:1',
            'average_consultation_time' => 'nullable|integer|min:5|max:120',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'existing_doctor_id' => 'nullable|exists:users,id',
        ]);

        // Vérifier si un médecin existe déjà avec ce numéro de téléphone
        $existingDoctor = User::where('phone', $validated['phone'])
            ->where('role', 'doctor')
            ->first();

        if ($existingDoctor) {
            // Le médecin existe déjà, créer uniquement une nouvelle affiliation (profil)
            // Vérifier si ce médecin a déjà un profil avec cette spécialité
            $existingProfile = $existingDoctor->doctorProfiles()
                ->where('specialty_id', $validated['specialty_id'])
                ->first();

            if ($existingProfile) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['specialty_id' => 'Ce médecin a déjà un profil avec cette spécialité.']);
            }

            // Créer le nouveau profil (affiliation)
            $existingDoctor->doctorProfiles()->create([
                'specialty_id' => $validated['specialty_id'],
                'qualification' => $validated['qualification'],
                'bio' => $validated['bio'] ?? null,
                'max_patients_per_day' => $validated['max_patients_per_day'] ?? 20,
                'average_consultation_time' => $validated['average_consultation_time'] ?? 30,
                'is_available' => true,
            ]);

            return redirect()->route('doctors.show', $existingDoctor)
                ->with('success', 'Nouvelle affiliation ajoutée avec succès pour le médecin existant.');
        }

        // Le médecin n'existe pas, créer un nouveau compte et profil
        $profilePhotoPath = null;
        if ($request->hasFile('profile_photo')) {
            $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        // Générer un mot de passe aléatoire
        $password = Str::random(12);

        // Créer l'utilisateur
        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($password),
            'role' => 'doctor',
            'profile_photo_path' => $profilePhotoPath,
        ]);

        // Créer le profil du médecin
        $user->doctorProfiles()->create([
            'specialty_id' => $validated['specialty_id'],
            'qualification' => $validated['qualification'],
            'bio' => $validated['bio'] ?? null,
            'max_patients_per_day' => $validated['max_patients_per_day'] ?? 20,
            'average_consultation_time' => $validated['average_consultation_time'] ?? 30,
            'is_available' => true,
        ]);

        // TODO: Envoyer un email au médecin avec ses identifiants

        return redirect()->route('doctors.index')
            ->with('success', 'Médecin créé avec succès. Mot de passe: ' . $password);
    }

    /**
     * Afficher les détails d'un médecin
     */
    public function show(User $doctor)
    {
        // $this->authorize('view', $doctor);
        $doctor->load([
            'doctorProfiles.specialty.department',
            'doctorProfiles.schedules',
            'doctorProfiles.unavailabilities',
            'schedules',
            'unavailabilities'
        ]);
        
        return view('doctors.show', compact('doctor'));
    }

    /**
     * Afficher le formulaire d'édition d'un médecin ou d'une affiliation
     */
    public function edit(User $doctor, Request $request)
    {
        $departments = Department::with('specialties')->get();
        $doctor->load('doctorProfiles.specialty.department');
        
        // Si un profileId est fourni dans la requête, charger ce profil spécifique
        $selectedProfile = null;
        $profileId = $request->query('profile');
        
        if ($profileId) {
            $selectedProfile = $doctor->doctorProfiles()->findOrFail($profileId);
        } else {
            // Sinon, charger le premier profil ou créer un nouveau
            $selectedProfile = $doctor->doctorProfiles->first();
        }
        
        return view('doctors.edit', compact('doctor', 'departments', 'selectedProfile'));
    }

    /**
     * Mettre à jour les informations d'un médecin
     */
    public function update(Request $request, User $doctor)
    {
        // $this->authorize('update', $doctor);

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($doctor->id),
            ],
            'phone' => [
                'required',
                'string',
                'max:20',
                Rule::unique('users')->ignore($doctor->id),
            ],
            'doctor_profile_id' => 'nullable|exists:doctor_profiles,id',
            'specialty_id' => 'required|exists:specialties,id',
            'qualification' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'max_patients_per_day' => 'nullable|integer|min:1',
            'average_consultation_time' => 'nullable|integer|min:5|max:120',
            'is_available' => 'boolean',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Gestion de la photo de profil
        if ($request->has('remove_photo') && $request->input('remove_photo') === '1') {
            // Supprimer la photo existante si elle existe
            if ($doctor->profile_photo_path) {
                Storage::disk('public')->delete($doctor->profile_photo_path);
                $validated['profile_photo_path'] = null;
            }
        } elseif ($request->hasFile('profile_photo')) {
            // Supprimer l'ancienne photo si elle existe
            if ($doctor->profile_photo_path) {
                Storage::disk('public')->delete($doctor->profile_photo_path);
            }
            // Stocker la nouvelle photo
            $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
            $validated['profile_photo_path'] = $profilePhotoPath;
        }

        // Mettre à jour l'utilisateur
        $doctor->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'profile_photo_path' => $validated['profile_photo_path'] ?? ($request->has('remove_photo') && $request->input('remove_photo') === '1' ? null : $doctor->profile_photo_path),
        ]);

        // Si un doctor_profile_id est fourni, mettre à jour ce profil spécifique
        if ($validated['doctor_profile_id']) {
            $profile = $doctor->doctorProfiles()->findOrFail($validated['doctor_profile_id']);
            
            // Vérifier si la spécialité a changé
            if ($profile->specialty_id != $validated['specialty_id']) {
                // Vérifier si le médecin a déjà un profil avec cette spécialité
                $existingProfile = $doctor->doctorProfiles()
                    ->where('specialty_id', $validated['specialty_id'])
                    ->where('id', '!=', $profile->id)
                    ->first();
                
                if ($existingProfile) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['specialty_id' => 'Ce médecin a déjà un profil avec cette spécialité. Veuillez en sélectionner un autre.']);
                }
            }
            
            // Mettre à jour le profil existant
            $profile->update([
                'specialty_id' => $validated['specialty_id'],
                'qualification' => $validated['qualification'],
                'bio' => $validated['bio'] ?? null,
                'max_patients_per_day' => $validated['max_patients_per_day'] ?? 20,
                'average_consultation_time' => $validated['average_consultation_time'] ?? 30,
                'is_available' => $validated['is_available'] ?? true,
            ]);
        } else {
            // Sinon, vérifier si le médecin a déjà un profil avec cette spécialité
            $existingProfile = $doctor->doctorProfiles()
                ->where('specialty_id', $validated['specialty_id'])
                ->first();
            
            if ($existingProfile) {
                // Mettre à jour le profil existant
                $existingProfile->update([
                    'qualification' => $validated['qualification'],
                    'bio' => $validated['bio'] ?? null,
                    'max_patients_per_day' => $validated['max_patients_per_day'] ?? 20,
                    'average_consultation_time' => $validated['average_consultation_time'] ?? 30,
                    'is_available' => $validated['is_available'] ?? true,
                ]);
            } else {
                // Créer une nouvelle affiliation
                $doctor->doctorProfiles()->create([
                    'specialty_id' => $validated['specialty_id'],
                    'qualification' => $validated['qualification'],
                    'bio' => $validated['bio'] ?? null,
                    'max_patients_per_day' => $validated['max_patients_per_day'] ?? 20,
                    'average_consultation_time' => $validated['average_consultation_time'] ?? 30,
                    'is_available' => $validated['is_available'] ?? true,
                ]);
            }
        }

        return redirect()->route('doctors.show', $doctor)
            ->with('success', 'Profil du médecin mis à jour avec succès');
    }

    /**
     * Supprimer un médecin
     */
    public function destroy(User $doctor)
    {
        // $this->authorize('delete', $doctor);
        
        $doctor->delete();

        return redirect()->route('doctors.index')
            ->with('success', 'Médecin supprimé avec succès');
    }

    /**
     * Basculer le statut actif/désactivé d'un médecin
     */
    public function toggleStatus(User $doctor)
    {
        // $this->authorize('update', $doctor);
        
        $doctor->update([
            'is_active' => !$doctor->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $doctor->is_active,
            'message' => $doctor->is_active ? 'Médecin activé avec succès' : 'Médecin désactivé avec succès'
        ]);
    }

    /**
     * Afficher le formulaire de gestion des horaires
     */
    public function showScheduleForm(User $doctor, Request $request)
    {
        // $this->authorize('update', $doctor);
        $doctor->load('doctorProfiles.specialty.department');
        
        // Si un profileId est fourni dans la requête, charger les horaires de ce profil
        $selectedProfile = null;
        $profileId = $request->query('profile');
        $schedules = collect();
        
        if ($profileId) {
            $selectedProfile = $doctor->doctorProfiles()->findOrFail($profileId);
            $schedules = $selectedProfile->schedules()->get()->keyBy('day_of_week');
        } else {
            // Sinon, charger le premier profil
            $selectedProfile = $doctor->doctorProfiles->first();
            if ($selectedProfile) {
                $schedules = $selectedProfile->schedules()->get()->keyBy('day_of_week');
            }
        }
        
        return view('doctors.schedule', compact('doctor', 'schedules', 'selectedProfile'));
    }

    /**
     * Mettre à jour les horaires du médecin pour une affiliation spécifique
     */
    public function updateSchedule(Request $request, User $doctor)
    {
        // $this->authorize('update', $doctor);

        $validated = $request->validate([
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
        ]);

        // Vérifier que le profil appartient au médecin
        $profile = $doctor->doctorProfiles()->findOrFail($validated['doctor_profile_id']);

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        // Valider d'abord toutes les entrées
        $rules = [];
        foreach ($days as $day) {
            $rules["{$day}_start"] = 'nullable|date_format:H:i';
            $rules["{$day}_end"] = 'nullable|date_format:H:i|after:' . $day . '_start';
        }
        
        $validated = array_merge($validated, $request->validate($rules));
        
        // Traiter chaque jour
        foreach ($days as $day) {
            $isAvailable = $request->has("{$day}_available") ? true : false;
            $startTime = $validated["{$day}_start"] ?? null;
            $endTime = $validated["{$day}_end"] ?? null;

            $schedule = $profile->schedules()->where('day_of_week', $day)->first();
            
            if ($isAvailable && $startTime && $endTime) {
                if ($schedule) {
                    $schedule->update([
                        'doctor_id' => $doctor->id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_available' => true,
                    ]);
                } else {
                    $profile->schedules()->create([
                        'doctor_id' => $doctor->id,
                        'day_of_week' => $day,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_available' => true,
                    ]);
                }
            } elseif ($schedule) {
                if ($isAvailable) {
                    // Si coché mais heures manquantes, mettre à jour avec des valeurs par défaut
                    $schedule->update([
                        'doctor_id' => $doctor->id,
                        'start_time' => $startTime ?: '09:00:00',
                        'end_time' => $endTime ?: '17:00:00',
                        'is_available' => true,
                    ]);
                } else {
                    // Si non coché, marquer comme non disponible
                    $schedule->update(['is_available' => false]);
                }
            } elseif ($isAvailable) {
                // Si coché mais pas d'horaire existant et heures manquantes, créer avec des valeurs par défaut
                $profile->schedules()->create([
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                    'start_time' => $startTime ?: '09:00:00',
                    'end_time' => $endTime ?: '17:00:00',
                    'is_available' => true,
                ]);
            }
        }

        return redirect()->route('doctors.schedule', ['doctor' => $doctor->id, 'profile' => $profile->id])
            ->with('success', 'Horaires mis à jour avec succès');
    }

    /**
     * Marquer un médecin comme indisponible pour une affiliation spécifique
     */
    public function markUnavailable(Request $request, User $doctor)
    {
        // $this->authorize('update', $doctor);

        $validated = $request->validate([
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
            'unavailable_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:255',
        ]);

        // Vérifier que le profil appartient au médecin
        $profile = $doctor->doctorProfiles()->findOrFail($validated['doctor_profile_id']);

        $profile->unavailabilities()->create([
            'doctor_id' => $doctor->id,
            'unavailable_date' => $validated['unavailable_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'reason' => $validated['reason'],
        ]);

        return redirect()->back()
            ->with('success', 'Indisponibilité enregistrée avec succès');
    }

    /**
     * Enregistrer un retard pour un médecin pour une affiliation spécifique
     */
    public function logDelay(Request $request, User $doctor)
    {
        // $this->authorize('update', $doctor);

        $validated = $request->validate([
            'doctor_profile_id' => 'required|exists:doctor_profiles,id',
            'delay_start' => 'required|date',
            'delay_duration' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        // Vérifier que le profil appartient au médecin
        $profile = $doctor->doctorProfiles()->findOrFail($validated['doctor_profile_id']);

        // Convertir delay_start en format datetime si nécessaire (datetime-local)
        $delayStart = $validated['delay_start'];
        if (strpos($delayStart, 'T') !== false) {
            // Format datetime-local: convertir en datetime
            $delayStart = str_replace('T', ' ', $delayStart) . ':00';
        }

        // Désactiver les retards précédents pour ce profil
        $profile->delays()
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $profile->delays()->create([
            'doctor_id' => $doctor->id,
            'delay_start' => $delayStart,
            'delay_duration' => $validated['delay_duration'],
            'reason' => $validated['reason'],
            'is_active' => true,
        ]);

        return redirect()->back()
            ->with('success', 'Retard enregistré avec succès');
    }
}
