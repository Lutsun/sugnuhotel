<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_number' => $this->room_number,
            'room_type_id' => $this->room_type_id,
            'floor' => $this->floor,
            'price_per_night' => (float) $this->price_per_night,
            'max_occupancy' => $this->max_occupancy,
            'status' => $this->status,
            'room_type' => new RoomTypeResource($this->whenLoaded('roomType')),
            'images' => RoomImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
