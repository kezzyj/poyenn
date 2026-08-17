<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enquiry extends Model
{
    use HasFactory;

    protected $fillable = [
    'platform_id',
    'customer_id',
    'product_id',
    'customer_name',
    'phone',
    'email',
    'message',
    'location',
    'status',
    'admin_notes',
];

public function customer(): BelongsTo
{
    return $this->belongsTo(Customer::class);
}

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}