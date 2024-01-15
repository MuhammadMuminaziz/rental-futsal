<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
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
            'note' => $this->note,
            'stars' => $this->stars,
            'doc_reviews' => $this->doc_reviews,
            'created_at' => $this->created_at->translatedFormat('d F Y H:i:s'),
        ];
    }
}
