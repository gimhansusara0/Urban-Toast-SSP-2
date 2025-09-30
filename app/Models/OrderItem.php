<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;

class OrderItem extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'order_id',
        'quantity',
        'price_each',
        'status',
        'purchased_at',
    ];

    /**
     * The customer who added this item to cart (or purchased it).
     * user_id -> customers.id
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

    /**
     * The product referenced by this item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * The order this item belongs to (nullable while in cart).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Convenience accessor for line total.
     */
    public function getLineTotalAttribute(): float
    {
        return (float) $this->price_each * $this->quantity;
    }
}
