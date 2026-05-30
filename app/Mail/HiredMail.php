<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class HiredMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application
    ) {
    }

    public function build()
    {
        return $this->subject('Welcome to the Team: ' . $this->application->job->title)
            ->html("
                <h2>Welcome Aboard!</h2>
                <p>Hello {$this->application->candidate->full_name},</p>
                <p>We are excited to officially welcome you to Hireflow as our new <strong>{$this->application->job->title}</strong>!</p>
                <p>All your onboarding details have been processed. We look forward to seeing the great things we'll achieve together.</p>
                <p>If you have any remaining questions before your first day, please don't hesitate to reach out.</p>
                <p>Best regards,<br>Hireflow Team</p>
            ");
    }
}
