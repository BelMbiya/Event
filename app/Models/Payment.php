<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ========================================
 * PAYMENT MODEL - GESTION DES PAIEMENTS
 * ========================================
 * 
 * Ce modèle gère les paiements effectués par les invités.
 * Il track les montants, méthodes de paiement et statuts
 * pour le suivi financier des événements.
 */

class Payment extends Model
{
    protected $fillable = [
        'guest_id',
        'amount',
        'method',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }
}
