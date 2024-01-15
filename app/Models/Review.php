<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory, HasUuids;
    protected $guarded = [];
    protected $casts = [
        'doc_reviews' => 'array'
    ];

    public function scopeGetRating($query, $id)
    {
        $reviews = $query->where('futsal_id', $id)->get();
        if (empty($reviews)) {
            return null;
        }
        $rating = 0;
        foreach ($reviews as $n) {
            $rating = $rating + $n->stars;
        }
        return ($rating / count($reviews));
    }
}
