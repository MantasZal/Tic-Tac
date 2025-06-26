<?php

namespace App\Models;

use App\Observers\AchievementObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([AchievementObserver::class])]
class Achievement extends Model
{
    protected $fillable = ['title', 'description', 'icon'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
}
