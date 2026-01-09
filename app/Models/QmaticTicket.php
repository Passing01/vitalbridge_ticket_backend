<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QmaticTicket extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'health_center_id',
        'service_id',
        'ticket_number',
        'sequence_number',
        'priority',
        'status',
        'counter_id',
        'agent_id',
        'called_at',
        'served_at',
        'completed_at',
        'wait_time',
        'service_time',
    ];

    protected $casts = [
        'sequence_number' => 'integer',
        'wait_time' => 'integer',
        'service_time' => 'integer',
        'called_at' => 'datetime',
        'served_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Définir l'ordre de priorité
    const PRIORITY_ORDER = [
        'urgent' => 1,
        'vip' => 2,
        'senior' => 3,
        'normal' => 4,
    ];

    /**
     * Relation avec le centre de santé
     */
    public function healthCenter(): BelongsTo
    {
        return $this->belongsTo(HealthCenter::class);
    }

    /**
     * Relation avec le service
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(QmaticService::class, 'service_id');
    }

    /**
     * Relation avec le guichet
     */
    public function counter(): BelongsTo
    {
        return $this->belongsTo(QmaticCounter::class, 'counter_id');
    }

    /**
     * Relation avec l'agent
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(QmaticUser::class, 'agent_id');
    }

    /**
     * Relation avec les appels
     */
    public function calls(): HasMany
    {
        return $this->hasMany(QmaticTicketCall::class, 'ticket_id');
    }

    /**
     * Appeler ce ticket
     */
    public function call(QmaticCounter $counter, QmaticUser $agent): void
    {
        $this->update([
            'status' => 'called',
            'counter_id' => $counter->id,
            'agent_id' => $agent->id,
            'called_at' => now(),
            'wait_time' => now()->diffInMinutes($this->created_at),
        ]);

        // Enregistrer l'appel dans l'historique
        $this->calls()->create([
            'counter_id' => $counter->id,
            'agent_id' => $agent->id,
            'called_at' => now(),
        ]);
    }

    /**
     * Rappeler ce ticket
     */
    public function recall(): void
    {
        $this->update([
            'called_at' => now(),
        ]);

        // Enregistrer l'appel dans l'historique
        $this->calls()->create([
            'counter_id' => $this->counter_id,
            'agent_id' => $this->agent_id,
            'called_at' => now(),
        ]);
    }

    /**
     * Marquer comme en cours de service
     */
    public function startServing(): void
    {
        $this->update([
            'status' => 'serving',
            'served_at' => now(),
        ]);
    }

    /**
     * Marquer comme servi
     */
    public function markAsServed(): void
    {
        $this->update([
            'status' => 'served',
            'completed_at' => now(),
            'service_time' => $this->served_at ? now()->diffInMinutes($this->served_at) : null,
        ]);
    }

    /**
     * Marquer comme absent
     */
    public function markAsAbsent(): void
    {
        $this->update([
            'status' => 'absent',
            'completed_at' => now(),
        ]);
    }

    /**
     * Marquer comme annulé
     */
    public function markAsCancelled(): void
    {
        $this->update([
            'status' => 'cancelled',
            'completed_at' => now(),
        ]);
    }

    /**
     * Remettre en file d'attente
     */
    public function requeue(): void
    {
        $this->update([
            'status' => 'waiting',
            'counter_id' => null,
            'agent_id' => null,
            'called_at' => null,
            'served_at' => null,
            'completed_at' => null,
        ]);
    }

    /**
     * Obtenir le niveau de priorité numérique
     */
    public function getPriorityLevel(): int
    {
        return self::PRIORITY_ORDER[$this->priority] ?? 999;
    }

    /**
     * Scope pour les tickets en attente
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    /**
     * Scope pour trier par priorité puis par date de création
     */
    public function scopeOrderByPriority($query)
    {
        return $query->orderByRaw('
            CASE priority
                WHEN "urgent" THEN 1
                WHEN "vip" THEN 2
                WHEN "senior" THEN 3
                WHEN "normal" THEN 4
                ELSE 5
            END
        ')->orderBy('created_at');
    }
}
