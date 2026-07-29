<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reservation_number' => $this->reservation_number,
            'check_in_date' => $this->check_in_date?->format('Y-m-d'),
            'check_out_date' => $this->check_out_date?->format('Y-m-d'),
            'number_of_adults' => $this->number_of_adults,
            'number_of_children' => $this->number_of_children,
            'total_price' => (float) $this->total_price,
            'status' => $this->status,
            'special_requests' => $this->special_requests,
            'created_at' => $this->created_at,
            'user' => new UserResource($this->whenLoaded('user')),
            'room' => new RoomResource($this->whenLoaded('room')),
            'services' => ReservationServiceResource::collection($this->whenLoaded('services')),
        ];
    }
}
