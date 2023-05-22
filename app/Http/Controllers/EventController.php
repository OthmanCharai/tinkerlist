<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventStoreRequest;
use App\Http\Requests\EventUpdateRequest;
use App\Http\Requests\GetEventRequest;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\LocationCollection;
use App\Models\Event;
use App\Services\Events\EventServiceInterface;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function __construct(protected EventServiceInterface $eventService)
    {

    }

    /**
     * Store new Event
     */
    public function store(EventStoreRequest $request): EventResource|ExceptionResource
    {
        return $this->eventService->addEvent($request->validated());
    }

    public function index(GetEventRequest $request): EventCollection|ExceptionResource
    {

        return $this->eventService->getEvents(array_merge(
            $request->validated(),
            ['per_page' => $request->input('per_page', 10)]
        ));
    }

    /**
     * Get an Event based on id
     */
    public function show(Event $event): EventResource
    {
        return $this->eventService->getEvent($event);
    }

    /**
     * Update an event
     */
    public function update(EventUpdateRequest $request, Event $event): EventResource|ExceptionResource
    {
        return $this->eventService->updateEvent($request->validated(), $event->load('invitees'));
    }

    /**
     * Delete a specif event
     */
    public function delete(Event $event): JsonResponse
    {
        return $this->eventService->deleteEvent($event->load('invitees'));
    }

    /**
     * Get Location for a specific date time
     */
    public function getLocationDateRange(GetEventRequest $request): LocationCollection|ExceptionResource
    {
        return $this->eventService->getLocationDateRange($request->validated());
    }
}
