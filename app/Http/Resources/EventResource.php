<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'time' => $this->time,
            'title' => $this->title,
            'location' => $this->location,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator' => UserResource::make($this->whenLoaded('user')),
            'invitees' => UserCollection::make($this->whenLoaded('invitees')),
            'weather' => WeatherResource::make($this->whenLoaded('weather')),
        ];
    }
}
