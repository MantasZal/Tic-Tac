<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameTable extends Model
{
    protected $table = 'games';
    public function players()
    {
        return $this->hasMany(Player::class);
    }
    protected $fillable = ['starter', 'difficulty'];
}
