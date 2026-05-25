<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InterviewInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application
    ) {}

    public function build()
    {
        return $this->subject('Interview Invitation')

            ->html("
                <h2>Interview Invitation</h2>

                <p>
                Hello {$this->application->candidate->full_name},
                </p>

                <p>
                You have been selected for an interview
                for the job:
                <strong>
                {$this->application->job->title}
                </strong>
                </p>

                <p>
                We will contact you soon with details.
                </p>
            ");
    }
}
