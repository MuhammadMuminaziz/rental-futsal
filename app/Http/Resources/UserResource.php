<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'role' => $this->role->name ?? $this->role_id,
            'team' => [
                'id' => $this->team->id ?? null,
                'name' => $this->team->name ?? null,
                'description' => $this->team->description ?? null,
                'avatar' => $this->avatar != null ? asset('futsal/avatar/') . "/" . $this->avatar : $this->avatar,
            ],
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'photo' => $this->photo,
            'created_at' => $this->created_at->translatedFormat('d F Y H:i:s')
        ];
    }
}
