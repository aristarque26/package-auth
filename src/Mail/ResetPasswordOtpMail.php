<?php

namespace Taibi\AuthAPI\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Taibi\AuthAPI\Models\User;

class ResetPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $otp;
    public int $expiresInMinutes;

    public function __construct(User $user, string $otp, int $expiresInMinutes = 15)
    {
        $this->user = $user;
        $this->otp = $otp;
        $this->expiresInMinutes = $expiresInMinutes;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔐 Réinitialisation de votre mot de passe - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'auth-api::emails.reset-password-otp',
            with: [
                'userName' => $this->user->name,
                'otp' => $this->otp,
                'expiresInMinutes' => $this->expiresInMinutes,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}