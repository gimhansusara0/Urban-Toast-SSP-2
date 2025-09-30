<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'product_id' => $this->product_id,
            'name'       => $this->product?->name ?? 'Unknown',
            'image'      => $this->product?->image,
            'quantity'   => $this->quantity,
            'price_each' => (float) $this->price_each,
            'line_total' => $this->line_total,
            'status'     => $this->status,
        ];
    }
}
