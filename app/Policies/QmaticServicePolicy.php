<?php

namespace App\Policies;

use App\Models\QmaticService;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QmaticServicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'reception';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, QmaticService $service): bool
    {
        // L'utilisateur doit être admin/reception et appartenir au même centre que le service
        $healthCenterId = $user->role === 'reception' ? $user->id : $user->health_center_id;
        return ($user->role === 'admin' || $user->role === 'reception') && 
               $service->health_center_id === $healthCenterId;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'reception';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, QmaticService $service): bool
    {
        $healthCenterId = $user->role === 'reception' ? $user->id : $user->health_center_id;
        return ($user->role === 'admin' || $user->role === 'reception') && 
               $service->health_center_id === $healthCenterId;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, QmaticService $service): bool
    {
        $healthCenterId = $user->role === 'reception' ? $user->id : $user->health_center_id;
        return ($user->role === 'admin' || $user->role === 'reception') && 
               $service->health_center_id === $healthCenterId;
    }
}
