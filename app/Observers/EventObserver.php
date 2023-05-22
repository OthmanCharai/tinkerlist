<?php

namespace App\Observers;

use App\Jobs\EventWeatherJob;
use App\Models\Event;

class EventObserver
{
    /**
     * When an Event Created a Job will be dispatched to get event weather
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        EventWeatherJob::dispatch($event); // dispatch the job in default queue
    }
}
