<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscountCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform_id',
        'code',
        'description',
        'type',
        'value',
        'minimum_order_amount',
        'maximum_discount_amount',
        'usage_limit',
        'usage_limit_per_customer',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'maximum_discount_amount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Status checks
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getIsStartedAttribute(): bool
    {
        return !$this->starts_at || $this->starts_at->isPast();
    }

    public function getIsUsageLimitReachedAttribute(): bool
    {
        return $this->usage_limit && $this->used_count >= $this->usage_limit;
    }

    public function getIsValidAttribute(): bool
    {
        return $this->is_active
            && $this->is_started
            && !$this->is_expired
            && !$this->is_usage_limit_reached;
    }

    // Display helpers
    public function getDisplayValueAttribute(): string
    {
        return $this->type === 'percentage'
            ? $this->value . '% off'
            : '₦' . number_format($this->value) . ' off';
    }

    // Check if a specific customer can use this code
    public function canBeUsedBy(int $customerId): bool
    {
        if (!$this->is_valid) {
            return false;
        }

        $customerUsageCount = $this->usages()
            ->where('customer_id', $customerId)
            ->count();

        return $customerUsageCount < $this->usage_limit_per_customer;
    }

    // Calculate discount for a given subtotal
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->minimum_order_amount && $subtotal < $this->minimum_order_amount) {
            return 0;
        }

        $discount = $this->type === 'percentage'
            ? ($subtotal * $this->value) / 100
            : (float) $this->value;

        if ($this->maximum_discount_amount && $discount > $this->maximum_discount_amount) {
            $discount = (float) $this->maximum_discount_amount;
        }

        return min($discount, $subtotal);
    }

    // Relationships
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function usages(): HasMany
    {
        return $this->hasMany(DiscountCodeUsage::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        return $query->active()
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }
}