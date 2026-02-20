<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'seller_id' => $this->seller_id,
            'seller' => new UserResource($this->whenLoaded('seller')),
            'title' => $this->title,
            'description' => $this->description,
            'category' => $this->category,
            'status' => $this->status,
            
            // Auction fields
            'base_price' => $this->when($this->isAuction() || $this->isHybrid(), $this->base_price),
            'required_deposit' => $this->when($this->isAuction() || $this->isHybrid(), $this->required_deposit),
            'current_highest_bid' => $this->when($this->isAuction() || $this->isHybrid(), $this->current_highest_bid),
            'highest_bidder_id' => $this->when($this->isAuction() || $this->isHybrid(), $this->highest_bidder_id),
            'current_winner_id' => $this->when($this->isAuction() || $this->isHybrid(), $this->current_winner_id),
            'start_time' => $this->when($this->isAuction() || $this->isHybrid(), $this->start_time?->toIso8601String()),
            'end_time' => $this->when($this->isAuction() || $this->isHybrid(), $this->end_time?->toIso8601String()),
            'finalization_deadline' => $this->when($this->isAuction() || $this->isHybrid(), $this->finalization_deadline?->toIso8601String()),
            
            // Direct sale fields
            'price' => $this->when($this->isDirectSale() || $this->isHybrid(), $this->price),
            'stock' => $this->when($this->isDirectSale() || $this->isHybrid(), $this->stock),
            'low_stock_threshold' => $this->when($this->isDirectSale() || $this->isHybrid(), $this->low_stock_threshold),
            
            'images' => ListingImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
