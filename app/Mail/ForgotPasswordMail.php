<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $email;

    /**
     * Create a new message instance.
     */
    public function __construct($otp, $email)
    {
        $this->otp = $otp;
        $this->email = $email;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Password Reset OTP')
                    ->view('emails.forgot-password')
                    ->with([
                        'otp' => $this->otp,
                        'name' => $this->email,
                    ]);
    }
}
