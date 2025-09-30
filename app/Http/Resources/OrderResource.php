<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'status'      => $this->status,
            'total'       => (float) $this->total,
            'purchased_at'=> $this->purchased_at,
            'items'       => OrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
