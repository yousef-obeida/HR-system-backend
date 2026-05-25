<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RejectionMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application
    ) {}

    public function build()
    {
        return $this->subject('Update on your application for ' . $this->application->job->title)
            ->html("
                <h2>Application Update</h2>
                <p>Hello {$this->application->candidate->full_name},</p>
                <p>Thank you for your interest in the <strong>{$this->application->job->title}</strong> position at Smart-HR and for taking the time to apply.</p>
                <p>After careful review of your application, we regret to inform you that we will not be moving forward with your candidacy at this time.</p>
                <p>We appreciate your interest in our company and wish you the best of luck in your job search.</p>
                <p>Best regards,<br>Smart-HR Recruitment Team</p>
            ");
    }
}
