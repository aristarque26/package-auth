<?php

namespace Taibi\AuthAPI\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordOtpNotification extends Notification
{
    use Queueable;

    protected string $otp;

    public function __construct(string $otp)
    {
        $this->otp = $otp;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe')
            ->line('Vous avez demandé une réinitialisation de votre mot de passe.')
            ->line('Voici votre code de vérification :')
            ->line("**{$this->otp}**")
            ->line('Ce code expirera dans 5 minutes.')
            ->line('Si vous n\'êtes pas à l\'origine de cette demande, ignorez cet email.');
    }
}