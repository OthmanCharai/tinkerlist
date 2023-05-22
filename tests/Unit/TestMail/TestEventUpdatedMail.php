<?php

namespace Tests\Unit\TestMail;

use App\Mail\EventUpdatedEmail;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TestEventUpdatedMail extends TestCase
{
    /**
     * Test Event Updated email build with success
     * @return void
     */
    public function test_event_updated_email(): void
    {
        // Create a dummy event
        $event = Event::factory()
            ->for(User::factory())
            ->create();

        // Create an instance of the EventUpdatedEmail Mailable with the event
        $mail = new EventUpdatedEmail($event);

        // Assert to see in view event title, date, time
        $mail->assertSeeInHtml($event->title);
        $mail->assertSeeInHtml($event->date);
        $mail->assertSeeInHtml($event->time);

    }

    /**
     * test event updated email not send
     * @return void
     */
    public function test_event_updated_email_not_send(): void
    {
        // Fake Mail to prevent mail from being sent
        Mail::fake();

        // Assert a EventUpdatedEmail was not sent...
        Mail::assertNotSent(EventUpdatedEmail::class);
    }

    /**
     * test event updated email send
     * @return void
     */
    public function test_event_updated_email_sent(): void
    {
        // Fake Mail to prevent mail from being sent
        Mail::fake();

        // Create a dummy event
        $event = Event::factory()
            ->for(User::factory())
            ->create();


        // Send the EventUpdatedEmail Mailable
        Mail::to('recipient@example.com')->send(new EventUpdatedEmail($event));

        // Assert that the email was sent
        Mail::assertSent(EventUpdatedEmail::class, function ($mail) use ($event) {
            return $mail->event->id === $event->id &&
                $mail->hasTo('recipient@example.com');
        });
    }
}
