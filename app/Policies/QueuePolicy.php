<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QueuePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Appointment $appointment): bool
    {
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut voir la file d'attente
     */
    public function viewQueue(User $user, User $doctor): bool
    {
        // Le médecin peut voir sa propre file d'attente
        // Un administrateur peut voir toutes les files d'attente
        // Un réceptionniste peut voir les files d'attente de son département
        return $user->id === $doctor->id || 
               $user->role === 'admin' || 
               ($user->role === 'reception' && $user->managedDepartments->contains('id', $doctor->doctorProfile->specialty->department_id));
    }

    /**
     * Vérifier si l'utilisateur peut appeler le patient suivant
     */
    public function callNext(User $user, User $doctor): bool
    {
        // Seul le médecin peut appeler le patient suivant
        return $user->id === $doctor->id && $user->role === 'doctor';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Vérifier si l'utilisateur peut mettre à jour un rendez-vous
     */
    public function update(User $user, Appointment $appointment): bool
    {
        // Le médecin peut mettre à jour les rendez-vous de sa propre file d'attente
        // Un administrateur peut tout mettre à jour
        // Un réceptionniste peut mettre à jour les rendez-vous de son département
        return $user->id === $appointment->doctor_id || 
               $user->role === 'admin' || 
               ($user->role === 'reception' && $user->managedDepartments->contains('id', $appointment->departments_id));
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Appointment $appointment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Appointment $appointment): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Appointment $appointment): bool
    {
        return false;
    }
}
