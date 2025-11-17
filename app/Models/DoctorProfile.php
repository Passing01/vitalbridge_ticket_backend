<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'specialty_id',
        'qualification',
        'bio',
        'max_patients_per_day',
        'average_consultation_time',
        'is_available'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'max_patients_per_day' => 'integer',
        'average_consultation_time' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(Specialty::class);
    }

    /**
     * Get the department through specialty.
     */
    public function department()
    {
        return $this->hasOneThrough(
            Department::class,
            Specialty::class,
            'id', // Foreign key on specialties table (specialty.id)
            'id', // Foreign key on departments table (department.id)
            'specialty_id', // Local key on doctor_profiles table
            'department_id' // Local key on specialties table (specialty.department_id)
        );
    }
    
    /**
     * Get the department attribute (helper method).
     */
    public function getDepartmentAttribute()
    {
        return $this->specialty ? $this->specialty->department : null;
    }

    /**
     * Get schedules for this profile.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_profile_id');
    }

    /**
     * Get unavailabilities for this profile.
     */
    public function unavailabilities(): HasMany
    {
        return $this->hasMany(DoctorUnavailability::class, 'doctor_profile_id');
    }

    /**
     * Get delays for this profile.
     */
    public function delays(): HasMany
    {
        return $this->hasMany(DoctorDelay::class, 'doctor_profile_id');
    }

    /**
     * Get the health center (reception) for this profile.
     * This is a helper method that accesses the health center through specialty->department.
     */
    public function getHealthCenterAttribute()
    {
        if (!$this->specialty || !$this->specialty->department) {
            return null;
        }
        
        return User::find($this->specialty->department->reception_id);
    }
}
