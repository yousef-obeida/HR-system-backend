<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplicationReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application
    ) {}

    public function build()
    {
        return $this->subject('Application Received - ' . $this->application->job->title)
            ->html("
                <h2>Application Received</h2>
                <p>Hello {$this->application->candidate->full_name},</p>
                <p>Thank you for applying for the <strong>{$this->application->job->title}</strong> position at Smart-HR.</p>
                <p>We have successfully received your CV and application details. Our HR team will review your profile and get back to you soon.</p>
                <p>Best regards,<br>Smart-HR Recruitment Team</p>
            ");
    }
}
