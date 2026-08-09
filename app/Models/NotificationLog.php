<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform_id',
        'customer_id',
        'order_id',
        'channel',
        'recipient',
        'subject',
        'message',
        'status',
        'provider',
        'provider_response',
        'sent_at',
        'failure_reason',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'sent_at' => 'datetime',
    ];

    // Status checks
    public function getIsSentAttribute(): bool
    {
        return $this->status === 'sent';
    }

    public function getIsFailedAttribute(): bool
    {
        return $this->status === 'failed';
    }

    public function getIsSmsAttribute(): bool
    {
        return $this->channel === 'sms';
    }

    public function getIsEmailAttribute(): bool
    {
        return $this->channel === 'email';
    }

    // Display helpers
    public function getChannelLabelAttribute(): string
    {
        return match ($this->channel) {
            'sms' => 'SMS',
            'email' => 'Email',
            default => ucfirst($this->channel),
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'sent' => 'Sent',
            'failed' => 'Failed',
            default => ucfirst($this->status),
        };
    }

    // Relationships
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Scopes
    public function scopeSms($query)
    {
        return $query->where('channel', 'sms');
    }

    public function scopeEmail($query)
    {
        return $query->where('channel', 'email');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    // Lifecycle helpers
    public function markAsSent(array $response = []): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'provider_response' => $response,
        ]);
    }

    public function markAsFailed(string $reason, array $response = []): void
    {
        $this->update([
            'status' => 'failed',
            'failure_reason' => $reason,
            'provider_response' => $response,
        ]);
    }
}