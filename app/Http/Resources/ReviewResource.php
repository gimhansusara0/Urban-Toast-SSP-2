<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'           => $this->id,
            'customer_id'  => $this->customer_id,
            'customer_name'=> $this->whenLoaded('customer', fn() => $this->customer?->name),
            'product_id'   => $this->product_id,
            'rating'       => (int) $this->rating,
            'title'        => $this->title,
            'body'         => $this->body,
            'approved'     => (bool) $this->approved,
            'created_at'   => $this->created_at?->toDateTimeString(),
            'updated_at'   => $this->updated_at?->toDateTimeString(),
        ];
    }
}
