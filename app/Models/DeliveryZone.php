<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform_id',
        'name',
        'state',
        'covered_cities',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessors
    public function getCitiesListAttribute(): array
    {
        if (empty($this->covered_cities)) {
            return [];
        }
        return array_map('trim', explode(',', $this->covered_cities));
    }

    // Relationships
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function rates(): HasMany
    {
        return $this->hasMany(DeliveryRate::class);
    }

    public function activeRates(): HasMany
    {
        return $this->hasMany(DeliveryRate::class)->where('is_active', true);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}