<?php

namespace App\Services\Events;

use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\LocationCollection;
use App\Jobs\SendInvitationJob;
use App\Mail\EventCanceledEmail;
use App\Mail\EventUpdatedEmail;
use App\Mail\InvitedEmail;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EventService implements EventServiceInterface
{
    const DATE_RANGE_QUERY = "CONCAT(date, ' ', time)";

    /**
     * Create a new event and associate the event with invitees
     * Location suppose to be validated from the front end, using google map api to get valid city name
     * I can use Geocoding/database to check if the location is valid.
     * in case of Geocoding means I will create a Rule that send a request to third api,it may slow down the code
     * we can after created an event create an event that will dispatch job to check if a location exist or not
     */
    public function addEvent(array $data): EventResource|ExceptionResource
    {
        // make sure to use only required params
        $invitees = $data['invitees'];
        unset($data['invitees']);

        try {
            //create new event
            Auth::user()->event()->save($event = new Event($data));

            // Associate Event wit invitees
            $event->invitees()->syncWithoutDetaching($invitees);

            // Get invitees Emails
            $inviteeEmails = collect(User::whereIn('id', $invitees)->select('email')->get())->pluck('email');

            // handle Event with invitees
            $this->handleEventInvitees($inviteeEmails, $event, InvitedEmail::class);

        } catch (\Exception $e) {
            return ExceptionResource::make($e);
        }

        return EventResource::make($event->load('user', 'invitees'));
    }

    public function getEvents(array $data): EventCollection|ExceptionResource
    {
        $perPage = $data['per_page']; // get perPage

        try {

            // select event between start| end date+time
            $query = $this->selectBetweenDateRange($data);
            $events = $query->with('weather', 'invitees')->paginate($perPage);

        } catch (\Exception $e) {
            return ExceptionResource::make($e);
        }

        // return data as EventCollection
        return EventCollection::make($events);
    }

    /**
     * Get a specif event
     */
    public function getEvent(Event $event): EventResource
    {
        return EventResource::make($event->load('weather', 'invitees'));
    }

    /**
     * Update an event
     */
    public function updateEvent(array $data, Event $event): EventResource|ExceptionResource
    {
        $invitees = $data['invitees']; //[1,3,4,5]
        unset($data['invitees']);

        // Get Old invitees
        $oldInvitees = $event->invitees;

        // Get old invitees,send update email.
        $oldInvited = $oldInvitees->whereIn('id', $invitees)->pluck('email');

        // Get deleted invitees send EventDeletedEvent
        $deletedInvitees = $oldInvitees->whereNotIn('id', $invitees)->pluck('email');

        // Get newInvitees IDs
        $newInviteesIds = array_values(collect($invitees)->diff($oldInvitees->pluck('id'))->toArray());

        // it longs but select only what's we use from db perform the app always
        // Get newInvitees Emails
        $inviteeEmails = collect(User::whereIn('id', $newInviteesIds)->select('email')->get())->pluck('email');

        try {

            // Update Event
            $event->update($data);

            // Associate invitees with the event
            $event->invitees()->sync($invitees);

            // check if $invitees emails not empty then send emails
            $this->handleEventInvitees($inviteeEmails, $event, InvitedEmail::class);

            // Send Updated Event Email
            $this->handleEventInvitees($oldInvited, $event, EventUpdatedEmail::class);

            // Send Event Canceled Event
            $this->handleEventInvitees($deletedInvitees, $event, EventCanceledEmail::class);

        } catch (\Exception $e) {
            return ExceptionResource::make($e);
        }

        return EventResource::make($event->load('user', 'invitees'));
    }

    /**
     * Delete a specif event
     */
    public function deleteEvent(Event $event): JsonResponse
    {
        // Delete Event
        $event->delete();

        return response()->json('event deleted with success')->setStatusCode(200);
    }

    /**
     * Get all Location between two date time
     */
    public function getLocationDateRange(array $data): LocationCollection|ExceptionResource
    {
        try {
            // select location between start| end date+time
            $query = $this->selectBetweenDateRange($data);
            $locations = $query->select('location')->get();

        } catch (\Exception $e) {
            return ExceptionResource::make($e);
        }

        return LocationCollection::make($locations);
    }

    /**
     * @param Collection $collection
     * @param Event $event
     * @param string $mailable
     * @return void
     */
    private function handleEventInvitees(Collection $collection, Event $event, string $mailable): void
    {
        // Dispatch unless collection not empty
        if ($collection->isNotEmpty()) {
            SendInvitationJob::dispatch($collection->toArray(), $event, $mailable);
        }
    }


    /**
     * Select from event where date. time btw two date
     */
    private function selectBetweenDateRange(array $data): mixed
    {
        return Event::where(DB::raw(self::DATE_RANGE_QUERY), '>=', $data['start_date'])
            ->where(DB::raw(self::DATE_RANGE_QUERY), '<=', $data['end_date']);
    }
}
