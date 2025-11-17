<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorUnavailability extends Model
{
    protected $fillable = [
        'doctor_id',
        'doctor_profile_id',
        'unavailable_date',
        'start_time',
        'end_time',
        'reason'
    ];

    protected $casts = [
        'unavailable_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i'
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the doctor profile for this unavailability.
     */
    public function doctorProfile(): BelongsTo
    {
        return $this->belongsTo(DoctorProfile::class, 'doctor_profile_id');
    }
}
