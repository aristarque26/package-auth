<?php

namespace Taibi\AuthAPI\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Taibi\AuthAPI\Models\EmailVerificationToken;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected EmailVerificationToken $token;

    public function __construct(EmailVerificationToken $token)
    {
        $this->token = $token;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = config('app.frontend_url', config('app.url'));
        $verificationUrl = $frontendUrl . '/verify-email?token=' . $this->token->token;

        return (new MailMessage)
            ->subject('✅ Vérification de votre adresse email - ' . config('app.name'))
            ->greeting('Bonjour ' . $notifiable->name . ' !')
            ->line('Merci de vous être inscrit sur ' . config('app.name') . ' !')
            ->line('Pour finaliser votre inscription, veuillez vérifier votre adresse email.')
            ->action('Vérifier mon email', $verificationUrl)
            ->line("**OU** utilisez ce code de vérification : **{$this->token->token}**")
            ->line("Ce lien expirera dans 24 heures.")
            ->line('Si vous n\'avez pas créé de compte, veuillez ignorer cet email.')
            ->salutation('Cordialement, ' . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'token' => $this->token->token,
            'expires_at' => $this->token->expires_at,
        ];
    }
}