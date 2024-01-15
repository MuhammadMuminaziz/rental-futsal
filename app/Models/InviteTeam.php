<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InviteTeam extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeMakeInvite($query, $user_id, $team_id)
    {
        foreach ($user_id as $id) {
            $query->create([
                'user_id' => $id,
                'team_id' => $team_id
            ]);
        }
    }

    public function scopeDeleteInvite($query, $team_id)
    {
        $users = $query->where('team_id', $team_id)->get();
        $users->delete();
    }
}
