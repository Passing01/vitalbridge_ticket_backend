<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorDelay extends Model
{
    protected $fillable = [
        'doctor_id',
        'delay_start',
        'delay_duration',
        'reason'
    ];

    protected $casts = [
        'delay_start' => 'datetime',
        'delay_duration' => 'integer'
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
