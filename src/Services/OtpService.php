<?php

namespace Taibi\AuthAPI\Services;

use Carbon\Carbon;
use Taibi\AuthAPI\Models\PasswordResetOtp;
use Taibi\AuthAPI\Models\User;

class OtpService
{
    public function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function createOtp(User $user): PasswordResetOtp
    {
        PasswordResetOtp::where('email', $user->email)->delete();

        return PasswordResetOtp::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'otp' => $this->generateOtp(),
            'expires_at' => Carbon::now()->addMinutes(5),
            'is_used' => false,
        ]);
    }

    public function verifyOtp(string $email, string $otp): ?PasswordResetOtp
    {
        $resetOtp = PasswordResetOtp::where('email', $email)
            ->where('otp', $otp)
            ->first();

        if (!$resetOtp) {
            return null;
        }

        if (!$resetOtp->isValid()) {
            return null;
        }

        return $resetOtp;
    }

    public function markAsUsed(PasswordResetOtp $resetOtp): void
    {
        $resetOtp->update(['is_used' => true]);
    }
}