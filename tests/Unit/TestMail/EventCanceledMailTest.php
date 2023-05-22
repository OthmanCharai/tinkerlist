<?php

namespace Tests\Unit\TestMail;

use App\Mail\EventCanceledEmail;
use App\Mail\EventUpdatedEmail;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EventCanceledMailTest extends TestCase
{
    /**
     * Test Event Canceled email build with success
     * @return void
     */
    public function test_event_canceled_email(): void
    {
        // Create a dummy event
        $event = Event::factory()
            ->for(User::factory())
            ->create();

        // Create an instance of the EventCanceledEmail Mailable with the event
        $mail = new EventCanceledEmail($event);

        // Assert to see in view event title, date, time
        $mail->assertSeeInHtml("Event Was Canceled ");

    }

    /**
     * test canceled email not send
     * @return void
     */
    public function test_event_canceled_email_not_send(): void
    {
        // Fake Mail to prevent mail from being sent
        Mail::fake();

        // Assert a EventCanceledEmail was not sent...
        Mail::assertNotSent(EventCanceledEmail::class);
    }

    /**
     * test canceled  email sent
     * @return void
     */
    public function test_event_canceled_email_sent(): void
    {
        // Fake Mail to prevent mail from being sent
        Mail::fake();

        // Create a dummy event
        $event = Event::factory()
            ->for(User::factory())
            ->create();


        // Send the EventCanceledEmail Mailable
        Mail::to('recipient@example.com')->send(new EventCanceledEmail($event));

        // Assert that the email was sent
        Mail::assertSent(EventCanceledEmail::class, function ($mail) use ($event) {
            return $mail->event->id === $event->id &&
                $mail->hasTo('recipient@example.com');
        });
    }

}
