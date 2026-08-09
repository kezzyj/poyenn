<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'order_status_history';

    protected $fillable = [
        'order_id',
        'status',
        'note',
        'changed_by_type',
        'changed_by_id',
    ];

    // Accessors
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Order Received',
            'confirmed' => 'Order Confirmed',
            'packed' => 'Order Packed',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Delivered',
            'failed_delivery' => 'Delivery Failed',
            'cancelled' => 'Cancelled',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Polymorphic-style resolver for who changed the status
    public function changedBy()
    {
        return match ($this->changed_by_type) {
            'admin' => Admin::find($this->changed_by_id),
            'delivery_agent' => DeliveryAgent::find($this->changed_by_id),
            'customer' => Customer::find($this->changed_by_id),
            default => null,
        };
    }
}