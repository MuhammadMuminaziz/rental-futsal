<?php

namespace App\Http\Resources;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FutsalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'name' => $this->user->name,
                'email' => $this->user->email,
                'photo' => $this->user->photo
            ],
            'name' => $this->name,
            'description' => $this->description,
            'facilities' => $this->facilities,
            'cancellation' => $this->cancellation,
            'whatsapp' => $this->whatsapp,
            'facebook' => $this->facebook,
            'instagram' => $this->instagram,
            'avatar' => $this->avatar != null ? asset('futsal/avatar/') . "/" . $this->avatar : $this->avatar,
            'rating' => $this->rating,
            'is_active' => $this->isActive ? true : false,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'created_at' => $this->created_at->translatedFormat('d F Y H:i:s'),
            'galleries' => $this->when(!request()->is('api/futsal'), FutGalResource::collection($this->galleries)),
        ];
    }
}
