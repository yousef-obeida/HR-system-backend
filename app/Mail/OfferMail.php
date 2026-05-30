<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OfferMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application
    ) {}

    public function build()
    {
        return $this->subject('Job Offer: ' . $this->application->job->title)
            ->html("
                <h2>Congratulations!</h2>
                <p>Hello {$this->application->candidate->full_name},</p>
                <p>We are thrilled to offer you the position of <strong>{$this->application->job->title}</strong> at Hireflow!</p>
                <p>We were very impressed by your skills and experience, and we believe you will be a fantastic addition to our team.</p>
                <p>We will send over the detailed offer letter and contract details shortly. Please feel free to reach out if you have any questions in the meantime.</p>
                <p>Welcome to the team!</p>
                <p>Best regards,<br>Hireflow Recruitment Team</p>
            ");
    }
}
