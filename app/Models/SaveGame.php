<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaveGame extends Model
{
    protected $table = 'game_results';

    protected $fillable = ['user_id', 'won', 'new_acievements'];
}
