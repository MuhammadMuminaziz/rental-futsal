<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Futsal extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function galleries()
    {
        return $this->hasMany(FutsalGallery::class);
    }

    public function scopeUpdateRating($query, $id, $rating)
    {
        $query->find($id)->update(['rating' => $rating]);
    }

    protected $casts = [
        'facilities' => 'array'
    ];
}
