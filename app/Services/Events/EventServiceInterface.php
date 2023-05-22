<?php

namespace App\Services\Events;

use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\LocationCollection;
use App\Models\Event;
use Illuminate\Http\JsonResponse;

interface EventServiceInterface
{
    /**
     * Store Event
     */
    public function addEvent(array $data): EventResource|ExceptionResource;

    /**
     * Get All the Events for a specific date interval
     */
    public function getEvents(array $data): EventCollection|ExceptionResource;

    /**
     * Get a specific event based on id
     */
    public function getEvent(Event $event): EventResource;

    /**
     * Update a specific event
     */
    public function updateEvent(array $data, Event $event): EventResource|ExceptionResource;

    /**
     * Delete a specific event
     */
    public function deleteEvent(Event $event): JsonResponse;

    /**
     * Get All Location for a date range
     */
    public function getLocationDateRange(array $data): LocationCollection|ExceptionResource;
}
