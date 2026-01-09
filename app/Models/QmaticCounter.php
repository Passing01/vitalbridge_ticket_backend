<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QmaticCounter extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'health_center_id',
        'code',
        'name',
        'service_ids',
        'is_active',
        'current_agent_id',
    ];

    protected $casts = [
        'service_ids' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec le centre de santé
     */
    public function healthCenter(): BelongsTo
    {
        return $this->belongsTo(HealthCenter::class);
    }

    /**
     * Relation avec l'agent actuel
     */
    public function currentAgent(): BelongsTo
    {
        return $this->belongsTo(QmaticUser::class, 'current_agent_id');
    }

    /**
     * Relation avec les tickets
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(QmaticTicket::class, 'counter_id');
    }

    /**
     * Vérifier si le guichet peut traiter un service donné
     */
    public function canHandleService(string $serviceId): bool
    {
        if (empty($this->service_ids)) {
            return true; // Si aucun service spécifié, peut tout traiter
        }

        return in_array($serviceId, $this->service_ids);
    }

    /**
     * Obtenir le ticket actuel en cours de traitement
     */
    public function currentTicket()
    {
        return $this->tickets()
                    ->whereIn('status', ['called', 'serving'])
                    ->latest('called_at')
                    ->first();
    }

    /**
     * Assigner un agent à ce guichet
     */
    public function assignAgent(QmaticUser $agent): void
    {
        $this->update(['current_agent_id' => $agent->id]);
    }

    /**
     * Libérer le guichet
     */
    public function releaseAgent(): void
    {
        $this->update(['current_agent_id' => null]);
    }
}
