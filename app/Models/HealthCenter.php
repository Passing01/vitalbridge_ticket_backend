<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HealthCenter extends User
{
    protected $table = 'users';

    /**
     * The "booting" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('reception', function ($builder) {
            $builder->where('role', 'reception');
        });

        static::creating(function ($model) {
            $model->role = 'reception';
        });
    }

    /**
     * Get the departments for the health center.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'reception_id', 'id');
    }
}
