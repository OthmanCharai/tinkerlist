<?php

namespace App\Jobs;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $emails, public Event $event, public string $emailType)
    {
        //
    }

    // tries number
    public int $tries = 3;

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        foreach ($this->emails as $email) {
            Mail::to($email)->send(
                new $this->emailType($this->event)
            );
        }
    }
}
