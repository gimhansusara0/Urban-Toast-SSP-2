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

//   Customer ID
    public function user(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

    // ProductID for the Item
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // OrderID
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // TOtal
    public function getLineTotalAttribute(): float
    {
        return (float) $this->price_each * $this->quantity;
    }
}
