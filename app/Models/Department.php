<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    protected $fillable = ['name', 'description', 'reception_id'];

    /**
     * Get the specialties for the department.
     */
    public function specialties(): HasMany
    {
        return $this->hasMany(Specialty::class);
    }

    /**
     * Get the health center that manages the department.
     */
    public function healthCenter(): BelongsTo
    {
        return $this->belongsTo(HealthCenter::class, 'reception_id');
    }
}
