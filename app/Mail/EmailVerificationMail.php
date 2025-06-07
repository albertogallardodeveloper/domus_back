<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $email;
    public string $code;

    /**
     * Ahora recibe el email (string) y el código.
     */
    public function __construct(string $email, string $code)
    {
        $this->email = $email;
        $this->code  = $code;
    }

    public function build()
    {
        return $this
            ->subject('Tu código de verificación Domus')
            ->view('emails.verify_email')
            ->with([
                'email' => $this->email,
                'code'  => $this->code,
            ]);
    }
}
