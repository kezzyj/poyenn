<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform_id',
        'order_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'flutterwave_ref',
        'flutterwave_tx_id',
        'flutterwave_payment_type',
        'paid_at',
        'failed_at',
        'failure_reason',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'metadata' => 'array',
    ];

    // Hide metadata from default JSON — can contain sensitive Flutterwave data
    protected $hidden = [
        'metadata',
    ];

    // Status accessors
    public function getIsSuccessfulAttribute(): bool
    {
        return $this->status === 'successful';
    }

    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    public function getIsCodAttribute(): bool
    {
        return $this->payment_method === 'cash_on_delivery';
    }

    public function getIsFlutterwaveAttribute(): bool
    {
        return $this->payment_method === 'flutterwave';
    }

    // Display helpers
    public function getMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'flutterwave' => 'Flutterwave',
            'cash_on_delivery' => 'Cash on Delivery',
            default => ucfirst($this->payment_method),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'successful' => 'Successful',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
            default => ucfirst($this->status),
        };
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

    // Scopes
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'successful');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Helpers — mark payment as successful
    public function markAsSuccessful(array $metadata = []): void
    {
        $this->update([
            'status' => 'successful',
            'paid_at' => now(),
            'metadata' => array_merge($this->metadata ?? [], $metadata),
        ]);
    }

    // Helpers — mark payment as failed
    public function markAsFailed(string $reason, array $metadata = []): void
    {
        $this->update([
            'status' => 'failed',
            'failed_at' => now(),
            'failure_reason' => $reason,
            'metadata' => array_merge($this->metadata ?? [], $metadata),
        ]);
    }
}