<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QmaticService extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'health_center_id',
        'code',
        'name',
        'icon',
        'image_url',
        'description',
        'priority_order',
        'is_active',
        'working_hours',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'working_hours' => 'array',
        'priority_order' => 'integer',
    ];

    /**
     * Relation avec le centre de santé
     */
    public function healthCenter(): BelongsTo
    {
        return $this->belongsTo(HealthCenter::class);
    }

    /**
     * Relation avec les tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(QmaticTicket::class, 'service_id');
    }

    /**
     * Obtenir les tickets en attente pour ce service
     */
    public function waitingTickets(): HasMany
    {
        return $this->tickets()
                    ->where('status', 'waiting')
                    ->orderBy('priority')
                    ->orderBy('created_at');
    }

    /**
     * Vérifier si le service est ouvert à une heure donnée
     */
    public function isOpenAt(\DateTimeInterface $dateTime): bool
    {
        if (!$this->is_active || !$this->working_hours) {
            return false;
        }

        $dayName = strtolower($dateTime->format('l')); // monday, tuesday, etc.
        $time = $dateTime->format('H:i');

        if (!isset($this->working_hours[$dayName])) {
            return false;
        }

        $hours = $this->working_hours[$dayName];
        return $time >= $hours['start'] && $time <= $hours['end'];
    }

    /**
     * Obtenir le prochain numéro de ticket pour ce service
     */
    public function getNextTicketNumber(): string
    {
        $today = now()->startOfDay();
        
        $lastTicket = $this->tickets()
                          ->whereDate('created_at', $today)
                          ->orderBy('sequence_number', 'desc')
                          ->first();

        $nextSequence = $lastTicket ? $lastTicket->sequence_number + 1 : 1;
        
        return $this->code . str_pad($nextSequence, 3, '0', STR_PAD_LEFT);
    }
}
