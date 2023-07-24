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
            'role' => $this->role->name ?? $this->role_id,
            // 'team' => $this->whenNotNull($this->team, function () {
            //     return [
            //         'name' => $this->team->name
            //     ];
            // }),
            'name' => $this->name,
            'slug' => $this->slug,
            'email' => $this->email,
            'photo' => $this->photo,
            'created_at' => $this->created_at->translatedFormat('d F Y H:i:s')
        ];
    }
}
