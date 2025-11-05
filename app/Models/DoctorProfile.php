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

    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class, 'doctor_id', 'user_id');
    }

    public function unavailabilities(): HasMany
    {
        return $this->hasMany(DoctorUnavailability::class, 'doctor_id', 'user_id');
    }

    public function delays(): HasMany
    {
        return $this->hasMany(DoctorDelay::class, 'doctor_id', 'user_id');
    }
}
