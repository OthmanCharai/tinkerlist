<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

// Extend from invitedEmail because for the moment we don't have more than one !=  btw EventUpdatedEmail,InvitedEmail
// This is scalable in future can extend from Mailable
class EventUpdatedEmail extends InvitedEmail
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
     * Overwrite parent InvitedEmail method
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invitation',
            with: [
                'title' => $this->event->title,
                'time' => $this->event->time,
                'date' => $this->event->date,
                'location' => $this->event->location,
                'emailType' => true,
            ]
        );
    }
}
