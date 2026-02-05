<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminApprovedPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $tempPassword;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $tempPassword)
    {
        $this->user = $user;
        $this->tempPassword = $tempPassword;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your password has been reset')
                    ->view('emails.admin-approved-reset')
                    ->with([
                        'user' => $this->user,
                        'tempPassword' => $this->tempPassword,
                    ]);
    }
}
