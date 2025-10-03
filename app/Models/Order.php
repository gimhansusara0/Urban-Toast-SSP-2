<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Customer;
use App\Models\OrderItem;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'total',
        'purchased_at',
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];


    //  The customer that placed this order.
    //   user_id references customers.id

    public function user(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

  
    //  Order items for this order.

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
