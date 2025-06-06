<?php

namespace App\Mail;

use App\Models\UserApp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;

    public function __construct(UserApp $user, string $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    public function build()
    {
        return $this
            ->subject('Tu código de verificación Domus')
            ->view('emails.verify_email') // vista Blade que vamos a crear
            ->with([
                'name' => $this->user->name,
                'code' => $this->code,
            ]);
    }
}
