<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameResult extends Model
{
    public function gameResults()
    {
        return $this->hasMany(GameResult::class);
    }

    protected $fillable = ['user_id', 'won', 'new_achievements'];
}
