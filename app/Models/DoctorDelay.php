<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorDelay extends Model
{
    protected $fillable = [
        'doctor_id',
        'doctor_profile_id',
        'delay_start',
        'delay_duration',
        'reason',
        'is_active'
    ];

    protected $casts = [
        'delay_start' => 'datetime',
        'delay_duration' => 'integer',
        'is_active' => 'boolean'
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * Get the doctor profile for this delay.
     */
    public function doctorProfile(): BelongsTo
    {
        return $this->belongsTo(DoctorProfile::class, 'doctor_profile_id');
    }
}
