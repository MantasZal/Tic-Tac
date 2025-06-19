<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\PlayerObserver;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([PlayerObserver::class])]
class Player extends Model
{
    protected $fillable = ['gameOver', 'data', 'palyer'];
}
