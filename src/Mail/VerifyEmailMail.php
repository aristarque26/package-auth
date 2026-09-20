<?php

namespace Taibi\AuthAPI\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Taibi\AuthAPI\Models\EmailVerificationToken;
use Taibi\AuthAPI\Models\User;

class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public EmailVerificationToken $token;
    public string $verificationUrl;

    public function __construct(User $user, EmailVerificationToken $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->verificationUrl = config('app.frontend_url', config('app.url')) . '/verify-email?token=' . $token->token;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ Vérification de votre adresse email - ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'auth-api::emails.verify-email',
            with: [
                'userName' => $this->user->name,
                'verificationUrl' => $this->verificationUrl,
                'token' => $this->token->token,
                'expiresAt' => $this->token->expires_at,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}