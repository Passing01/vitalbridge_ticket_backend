<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QmaticTicketCall extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'ticket_id',
        'counter_id',
        'agent_id',
        'called_at',
    ];

    protected $casts = [
        'called_at' => 'datetime',
    ];

    /**
     * Relation avec le ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(QmaticTicket::class, 'ticket_id');
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
}
