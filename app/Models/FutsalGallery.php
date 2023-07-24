<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FutsalGallery extends Model
{
    use HasFactory, Sluggable;

    protected $guarded = [];

    public function futsal()
    {
        return $this->belongsTo(Futsal::class);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
