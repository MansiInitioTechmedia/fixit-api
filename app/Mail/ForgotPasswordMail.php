<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ForgotPasswordMail extends Mailable
{
    public $otp;
    public $email;
    public $name;

    public function __construct($otp, $email, $name)
    {
        $this->otp = $otp;
        $this->email = $email;
        $this->name = $name;
    }

    public function build()
    {
        return $this->subject('Password Reset OTP')
                    ->view('emails.forgot-password')
                    ->with([
                        'otp' => $this->otp,
                        'email' => $this->email,
                        'name' => $this->name,
                    ]);
    }

}
