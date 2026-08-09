<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'delivery_zone_id',
        'name',
        'price',
        'estimated_days_min',
        'estimated_days_max',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Accessors
    public function getEstimatedDaysLabelAttribute(): string
    {
        if ($this->estimated_days_min === $this->estimated_days_max) {
            return $this->estimated_days_min . ' business day' . ($this->estimated_days_min > 1 ? 's' : '');
        }
        return "{$this->estimated_days_min}-{$this->estimated_days_max} business days";
    }

    // Relationships
    public function zone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class, 'delivery_zone_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}