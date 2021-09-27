<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ForgetPassword extends Mailable
{
    public $email;
    public $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->email,
        ], false));
        return $this
            ->subject('Reset Password')
            ->markdown('emails.auth.forgetPassword')
            ->with([
                'token' => $this->token,
                'email' => $this->email,
                'url'   => $url,
            ]);
    }
}
