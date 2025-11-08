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
        $doctors = User::where('role', 'doctor')
            ->with(['doctorProfile.specialty.department'])
            ->paginate(10);

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
     * Enregistrer un nouveau médecin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20|unique:users',
            'specialty_id' => 'required|exists:specialties,id',
            'qualification' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'max_patients_per_day' => 'nullable|integer|min:1',
            'average_consultation_time' => 'nullable|integer|min:5|max:120',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

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
        $user->doctorProfile()->create([
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
        $doctor->load(['doctorProfile.specialty.department', 'schedules', 'unavailabilities']);
        
        return view('doctors.show', compact('doctor'));
    }

    /**
     * Afficher le formulaire d'édition d'un médecin
     */
    public function edit(User $doctor)
    {
        
        $departments = Department::with('specialties')->get();
        $doctor->load('doctorProfile');
        
        return view('doctors.edit', compact('doctor', 'departments'));
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

        // Mettre à jour le profil du médecin
        $doctor->doctorProfile()->update([
            'specialty_id' => $validated['specialty_id'],
            'qualification' => $validated['qualification'],
            'bio' => $validated['bio'] ?? null,
            'max_patients_per_day' => $validated['max_patients_per_day'] ?? 20,
            'average_consultation_time' => $validated['average_consultation_time'] ?? 30,
            'is_available' => $validated['is_available'] ?? true,
        ]);

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
    public function showScheduleForm(User $doctor)
    {
        // $this->authorize('update', $doctor);
        $schedules = $doctor->schedules()->get()->keyBy('day_of_week');
        
        return view('doctors.schedule', compact('doctor', 'schedules'));
    }

    /**
     * Mettre à jour les horaires du médecin
     */
    public function updateSchedule(Request $request, User $doctor)
    {
        // $this->authorize('update', $doctor);

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        
        // Valider d'abord toutes les entrées
        $rules = [];
        foreach ($days as $day) {
            $rules["{$day}_start"] = 'nullable|date_format:H:i';
            $rules["{$day}_end"] = 'nullable|date_format:H:i|after:' . $day . '_start';
        }
        
        $validated = $request->validate($rules);
        
        // Traiter chaque jour
        foreach ($days as $day) {
            $isAvailable = $request->has("{$day}_available") ? true : false;
            $startTime = $validated["{$day}_start"] ?? null;
            $endTime = $validated["{$day}_end"] ?? null;

            $schedule = $doctor->schedules()->where('day_of_week', $day)->first();
            
            if ($isAvailable && $startTime && $endTime) {
                if ($schedule) {
                    $schedule->update([
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'is_available' => true,
                    ]);
                } else {
                    $doctor->schedules()->create([
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
                $doctor->schedules()->create([
                    'day_of_week' => $day,
                    'start_time' => $startTime ?: '09:00:00',
                    'end_time' => $endTime ?: '17:00:00',
                    'is_available' => true,
                ]);
            }
        }

        return redirect()->route('doctors.schedule', $doctor)
            ->with('success', 'Horaires mis à jour avec succès');
    }

    /**
     * Marquer un médecin comme indisponible
     */
    public function markUnavailable(Request $request, User $doctor)
    {
        // $this->authorize('update', $doctor);

        $validated = $request->validate([
            'unavailable_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'reason' => 'required|string|max:255',
        ]);

        $doctor->unavailabilities()->create($validated);

        return redirect()->back()
            ->with('success', 'Indisponibilité enregistrée avec succès');
    }

    /**
     * Enregistrer un retard pour un médecin
     */
    public function logDelay(Request $request, User $doctor)
    {
        // $this->authorize('update', $doctor);

        $validated = $request->validate([
            'delay_start' => 'required|date',
            'delay_duration' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $doctor->delays()->create($validated);

        return redirect()->back()
            ->with('success', 'Retard enregistré avec succès');
    }
}
