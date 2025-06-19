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
        // ✅ Example 1: Log a message
        Log::info('New achievement created: ' . $achievement->title);

        // ✅ Example 2: Send a notification (optional)
        // Notification::route('mail', 'admin@example.com')
        //     ->notify(new AchievementCreatedNotification($achievement));

        // ✅ Example 3: Broadcast a real-time event (optional)
        // event(new NewAchievementCreated($achievement));
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
