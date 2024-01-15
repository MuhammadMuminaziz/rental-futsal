<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FutGalResource extends JsonResource
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
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'isBackground' => $this->isBackground,
            'photo' => $this->photo != null ? asset('futsal/gallery/') . "/" . $this->photo : $this->photo,
            'created_at' => $this->created_at->translatedFormat('d F Y H:i:s')
        ];
    }
}
