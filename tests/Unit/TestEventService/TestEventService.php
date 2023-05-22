<?php

namespace Tests\Unit\TestEventService;

use App\Http\Controllers\EventController;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Http\Resources\ExceptionResource;
use App\Jobs\SendInvitationJob;
use App\Models\Event;
use App\Models\User;
use App\Observers\EventObserver;
use App\Services\Events\EventService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;
use Illuminate\Support\Facades\Event as EventFacade;


class TestEventService extends TestCase
{
    use RefreshDatabase;

    const URL = "http://localhost:8070/api/event";

    /**
     * Test store event function
     * @return void
     */
    public function test_add_event_function(): void
    {
        // Mock the Job Bus
        Bus::fake();

        // Your test code here

        // Assert that the job was not dispatched
        Bus::assertNotDispatched(SendInvitationJob::class);

        // Get Token
        $token = $this->login_user();

        // Create users to use as invitees
        $invitees = User::factory()->count(3)->create();

        $invitees = $invitees->pluck('id')->toArray();


        // Define the event data
        $eventData = Event::factory([

            'date' => Carbon::parse('2029-05-21')->toDateString(),
            'time' => '19:29:59',
            'location' => 'Tanger',

        ])->make()->toArray();

        $eventData['invitees'] = $invitees;

        // Send a POST request to the addEvent endpoint with the event data and JWT token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post(self::URL . '/create', $eventData);

        // Assert that the response has a successful status code
        $response->assertStatus(201);

        // Remove the timestamps from the expected data
        $expectedDataWithoutTimestamps = Arr::except($eventData, ['created_at', 'updated_at', 'invitees']);


        // Assert that the event was created in the database
        $this->assertDatabaseHas('events', $expectedDataWithoutTimestamps);

        // Assert that the response contains the created event data
        $response->assertJson([
            'data' => $expectedDataWithoutTimestamps,
        ]);
    }


    public function test_add_event_request_validation_error(): void
    {
        // Get Token
        $token = $this->login_user();

        // Fake users to use as invitees
        $invitees = User::factory()->count(3)->make();

        $invitees = $invitees->pluck('id')->toArray();


        // Define the event data
        $eventData = [];

        $eventData['invitees'] = $invitees;

        // Send a POST request to the addEvent endpoint with the event data and JWT token
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post(self::URL . '/create', $eventData);

        // Assert that the response has a validation exception  status code
        $response->assertStatus(302);
    }

    /**
     * Test getEvent using directly service
     * @return void
     */
    public function test_get_events(): void
    {
        // Define the test data for the getEvents function
        $data = [
            'per_page' => 5,
            "start_date" => "2001-11-23 21:37:32",
            "end_date" => "20022-11-23 21:37:32",
        ];

        // Create an instance of the EventService
        $eventService = new EventService();

        // Call the getEvents function
        $result = $eventService->getEvents($data);

        // Assert that the result is an instance of EventCollection or ExceptionResource
        $this->assertInstanceOf(EventCollection::class, $result);

    }

    public function test_get_events_throw_controller()
    {
        // Get Token
        $token = $this->login_user();

        // Define the test data for the getEvents function
        $data = [
            'per_page' => 5,
            "start_date" => "2001-11-23 21:37:32",
            "end_date" => "2022-11-23 21:37:32",
        ];

        // Set the Authorization header with the JWT token
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Send a GET request to the getEvents endpoint with the test data and headers
        $response = $this->withHeaders($headers)->json('GET', self::URL . '/index', $data);

        // Assert that the response has a successful status code
        $response->assertStatus(200);


    }

    public function test_get_events_throw_controller_data_not_validated()
    {
        // Get Token
        $token = $this->login_user();

        // Define the test data for the getEvents function
        $data = [

            'per_page' => 5
        ];

        // Set the Authorization header with the JWT token
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Send a GET request to the getEvents endpoint with the test data and headers
        $response = $this->withHeaders($headers)->json('GET', self::URL . '/index', $data);

        // Assert that the response has errors
        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'start_date' => [
                        'The start date field is required.',
                    ],
                    'end_date' => [
                        'The end date field is required.',
                    ],
                ],
            ]);

        // Assert Json Validation error
        $response->assertJsonValidationErrors([
            'start_date',
            'end_date',
        ]);

    }

    /**
     * @return void
     */
    public function test_get_event_throw_controller(): void
    {
        //Get Token
        $token = $this->login_user();

        // Create an event
        $event = Event::factory()
            ->for(User::factory())
            ->create();

        // Header
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Send a GET request to the getEvent endpoint with the event ID
        $response = $this->withHeaders($headers)->get(self::URL . '/show/' . $event->id);

        // Assert that the response has a successful status code
        $response->assertStatus(200);

        // Assert that the response contains the expected event data
        $response->assertJson([
            'data' => [
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
            ]
        ]);
    }

    /**
     * test get event throw controller not found model 404
     * @return void
     */
    public function test_get_event_throw_controller_not_found_model(): void
    {
        //Get Token
        $token = $this->login_user();

        // Header
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Send a GET request to the getEvent endpoint with the event ID
        $response = $this->withHeaders($headers)->get(self::URL . '/show/' . 100);

        // Assert that the response has a successful status code
        $response->assertStatus(404);

    }

    /**
     * test update event with valid data
     * @return void
     */
    public function test_update_event_with_valid_data(): void
    {
        // Mock the Job Bus
        Bus::fake();

        // Your test code here

        // Assert that the job was not dispatched
        Bus::assertNotDispatched(SendInvitationJob::class);

        //Get Token
        $token = $this->login_user();

        // Header
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Create an event
        $event = Event::factory()
            ->for(User::factory())
            ->create();

        // Fake users to use as invitees
        $invitees = User::factory()->count(3)->create();

        $invitees = $invitees->pluck('id')->toArray();

        // Define the updated event data
        $updatedData = [
            'title' => 'Updated Event',
            'description' => 'This is an updated event',
            "invitees" => $invitees,

        ];

        // Send a PUT request to the updateEvent endpoint with the event ID and updated data
        $response = $this->withHeaders($headers)->put(self::URL . '/update/' . $event->id, array_merge($event->toArray(), $updatedData));

        // Assert that the response has a successful status code
        $response->assertStatus(200);

        // Refresh the event from the database to get the updated data
        $event->refresh();

        // Assert that the event has been updated with the new data
        $this->assertEquals('Updated Event', $event->title);
        $this->assertEquals('This is an updated event', $event->description);

    }

    public function test_update_event_with_invalid_data()
    {
        //Get Token
        $token = $this->login_user();

        // Header
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Create an event
        $event = Event::factory()
            ->for(User::factory())
            ->create();

        // Send a PUT request to the updateEvent endpoint with the event ID and invalid data
        $response = $this->withHeaders($headers)->put(self::URL . '/update/' . $event->id, []);


        // Assert that the response has a validation error status code
        $response->assertStatus(302);

    }

    public function test_delete_event_throw_controller()
    {
        //Get Token
        $token = $this->login_user();

        // Header
        $headers = ['Authorization' => 'Bearer ' . $token];

        // Create an event
        $event = Event::factory()
            ->for(User::factory())
            ->create();

        // Send a DELETE request to the delete-event endpoint
        $response = $this->withHeaders($headers)->delete(self::URL . '/delete/' . $event->id);

        // Assert that the response has a successful status code
        $response->assertStatus(200);

        // Assert that the event is no longer present in the database
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_get_location_date_range()
    {

        //Get Token
        $token = $this->login_user();

        // Header
        $headers = ['Authorization' => 'Bearer ' . $token];
        // Define the test data for the date range
        $data = [
            "start_date" => "2001-11-23 21:37:32",
            "end_date" => "2001-12-23 21:37:32",

        ];

        // Send a GET request to the getEvents endpoint with the test data and headers
        $response = $this->withHeaders($headers)->json('GET', self::URL . '/index', $data);

        $response->assertStatus(200);
    }


    /**
     * @return string
     */
    private function login_user(): string
    {
        // Create a user
        $user = User::factory()->create();

        // Generate a JWT token for the user
        return auth()->login($user);
    }

}
