<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

// Extend from invitedEmail because for the moment we don't have more than one !=  btw EventCanceledEmail,InvitedEmail
// This is scalable in future can extend from Mailable
class EventCanceledEmail extends InvitedEmail
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Event $event)
    {
        parent::__construct($this->event);
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.event-deleted',
            with: [
                'title' => 'Event Was Canceled ', // Give ability to You Are not invited any more
            ]
        );
    }
}
