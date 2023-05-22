<?php

namespace Tests\Unit\TestMail;

use App\Mail\InvitedEmail;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TestInvitedMail extends TestCase
{

    /**
     * Test Invited email build with success
     * @return void
     */
    public function test_invited_email(): void
    {
        // Create a dummy event
        $event = Event::factory()
            ->for(User::factory())
            ->create();

        // Create an instance of the InvitedEmail Mailable with the event
        $mail = new InvitedEmail($event);

        // Assert to see in view event title, date, time
        $mail->assertSeeInHtml($event->title);
        $mail->assertSeeInHtml($event->date);
        $mail->assertSeeInHtml($event->time);

    }

    /**
     * test_invited_email_not_send
     * @return void
     */
    public function test_invited_email_not_send(): void
    {
        // Fake Mail to prevent mail from being sent
        Mail::fake();

        // Assert a InvitedEmail was not sent...
        Mail::assertNotSent(InvitedEmail::class);
    }

    /**
     * test_invited_email_sent
     * @return void
     */
    public function test_invited_email_sent(): void
    {
        // Fake Mail to prevent mail from being sent
        Mail::fake();

        // Create a dummy event
        $event = Event::factory()
            ->for(User::factory())
            ->create();


        // Send the InvitedEmail Mailable
        Mail::to('recipient@example.com')->send(new InvitedEmail($event));

        // Assert that the email was sent
        Mail::assertSent(InvitedEmail::class, function ($mail) use ($event) {
            return $mail->event->id === $event->id &&
                $mail->hasTo('recipient@example.com');
        });
    }


}
