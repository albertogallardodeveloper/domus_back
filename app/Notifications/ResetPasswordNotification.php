<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Construye la URL apuntando a tu app cliente:
        $url = config('app.client_url')
             . "/reset-password?token={$this->token}&email={$notifiable->email}";

        return (new MailMessage)
            ->subject('Restablece tu contraseña')
            ->line('Has solicitado restablecer tu contraseña.')
            ->action('Restablecer contraseña', $url)
            ->line('Si no fuiste tú, ignora este correo.');
    }
}
