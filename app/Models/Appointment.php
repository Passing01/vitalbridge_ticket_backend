<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Appointment extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'patient_id',
        'doctor_id',
        'reception_id',
        'departments_id',
        'specialties_id',
        'appointment_date',
        'status',
        'notes',
        'queue_position',
        'is_urgent',
        'is_absent',
        'is_being_served',
        'called_at',
        'served_at',
        'missed_calls'
    ];

    protected $casts = [
        'appointment_date' => 'datetime',
        'called_at' => 'datetime',
        'served_at' => 'datetime',
        'is_urgent' => 'boolean',
        'is_absent' => 'boolean',
        'is_being_served' => 'boolean'
    ];
    
    protected $dates = [
        'appointment_date',
        'called_at',
        'served_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function reception()
    {
        return $this->belongsTo(User::class, 'reception_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'departments_id');
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class, 'specialties_id');
    }

    /**
     * Scope pour les rendez-vous en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'scheduled')
                    ->where('is_absent', false)
                    ->whereNull('served_at');
    }

    /**
     * Scope pour les rendez-vous urgents
     */
    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true);
    }

    /**
     * Marquer le patient comme appelé
     */
    public function markAsCalled()
    {
        $this->update([
            'is_being_served' => true,
            'called_at' => now(),
            'missed_calls' => $this->missed_calls + 1
        ]);
    }

    /**
     * Marquer le patient comme servi
     */
    public function markAsServed()
    {
        $this->update([
            'is_being_served' => false,
            'served_at' => now(),
            'status' => 'completed'
        ]);
    }

    /**
     * Marquer le patient comme absent
     */
    public function markAsAbsent()
    {
        $this->update([
            'is_absent' => true,
            'is_being_served' => false,
            'status' => 'cancelled'
        ]);
    }

    /**
     * Réinsérer un patient absent dans la file d'attente
     */
    public function requeue()
    {
        // Trouver la prochaine position disponible dans les 10 prochains
        $nextPosition = $this->doctor->appointments()
            ->where('queue_position', '>', $this->queue_position)
            ->where('queue_position', '<=', $this->queue_position + 10)
            ->max('queue_position') + 1;

        if (is_null($nextPosition)) {
            $nextPosition = $this->queue_position + 1;
        }

        $this->update([
            'is_absent' => false,
            'is_being_served' => false,
            'status' => 'scheduled',
            'queue_position' => $nextPosition,
            'missed_calls' => 0
        ]);

        // Réorganiser les positions suivantes
        $this->doctor->appointments()
            ->where('queue_position', '>=', $nextPosition)
            ->where('id', '!=', $this->id)
            ->increment('queue_position');

        return $this;
    }

    /**
     * Obtenir la position dans la file d'attente
     */
    public function getQueuePositionAttribute($value)
    {
        if ($this->is_urgent) {
            return 'Urgent';
        }
        return $value;
    }
}
