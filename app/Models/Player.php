<?php

namespace App\Models;

use App\Enums\GameDificultyEnum;
use App\Observers\PlayerObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([PlayerObserver::class])]
class Player extends Model
{
    protected $fillable = ['gameOver', 'data', 'player', 'started', 'difficulty', 'game_id'];

    // protected function casts(): array
    // {
    //     return [
    //         'gameOver' => 'boolean',
    //         'difficulty' => GameDificultyEnum::class
    //     ];
    // }
    public function game()
    {
        return $this->belongsTo(GameTable::class);
    }
}
