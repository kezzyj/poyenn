<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform_id',
        'order_id',
        'delivery_agent_id',
        'status',
        'assigned_at',
        'picked_up_at',
        'delivered_at',
        'failed_at',
        'failure_reason',
        'agent_notes',
        'proof_of_delivery',
        'agent_commission',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
        'agent_commission' => 'decimal:2',
    ];

    // Status checks
    public function getIsAssignedAttribute(): bool
    {
        return $this->status === 'assigned';
    }

    public function getIsPickedUpAttribute(): bool
    {
        return $this->status === 'picked_up';
    }

    public function getIsInTransitAttribute(): bool
    {
        return $this->status === 'in_transit';
    }

    public function getIsDeliveredAttribute(): bool
    {
        return $this->status === 'delivered';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    // Display helpers
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'assigned' => 'Assigned to Agent',
            'picked_up' => 'Picked Up',
            'in_transit' => 'In Transit',
            'delivered' => 'Delivered',
            'failed' => 'Delivery Failed',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    // Proof of delivery image URL
    public function getProofOfDeliveryUrlAttribute(): ?string
    {
        return $this->proof_of_delivery
            ? asset('storage/' . $this->proof_of_delivery)
            : null;
    }

    // Relationships
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(DeliveryAgent::class, 'delivery_agent_id');
    }

    // Scopes
    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', ['assigned', 'picked_up', 'in_transit']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'delivered');
    }

    // Lifecycle helpers
    public function markAsPickedUp(): void
    {
        $this->update([
            'status' => 'picked_up',
            'picked_up_at' => now(),
        ]);
    }

    public function markAsInTransit(): void
    {
        $this->update(['status' => 'in_transit']);
    }

    public function markAsDelivered(?string $proofImage = null): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'proof_of_delivery' => $proofImage,
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_reason' => $reason,
        ]);
    }
}