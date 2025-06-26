<?php

namespace App\Observers;

use App\Models\Achievement;
use Illuminate\Support\Facades\Log;

class AchievementObserver
{
    /**
     * Handle the Achievement "created" event.
     */
    public function created(Achievement $achievement): void
    {

        Log::info('New achievement created: '.$achievement->title);
    }

    /**
     * Handle the Achievement "updated" event.
     */
    public function updated(Achievement $achievement): void
    {
        //
    }

    /**
     * Handle the Achievement "deleted" event.
     */
    public function deleted(Achievement $achievement): void
    {
        //
    }

    /**
     * Handle the Achievement "restored" event.
     */
    public function restored(Achievement $achievement): void
    {
        //
    }

    /**
     * Handle the Achievement "force deleted" event.
     */
    public function forceDeleted(Achievement $achievement): void
    {
        //
    }
}
