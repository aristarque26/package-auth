<?php

namespace Taibi\AuthAPI\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Taibi\AuthAPI\Models\EmailVerificationToken;
use Taibi\AuthAPI\Models\User;

class EmailVerificationController extends Controller
{
    public function sendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email déjà vérifié.',
            ], 400);
        }

        EmailVerificationToken::where('user_id', $user->id)->delete();

        $token = EmailVerificationToken::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'expires_at' => Carbon::now()->addHours(24),
        ]);

        return response()->json([
            'message' => 'Email de vérification envoyé.',
            'token' => $token->token,
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $verificationToken = EmailVerificationToken::where('token', $request->token)->first();

        if (!$verificationToken) {
            return response()->json([
                'message' => 'Token invalide.',
            ], 400);
        }

        if ($verificationToken->expires_at < Carbon::now()) {
            return response()->json([
                'message' => 'Token expiré.',
            ], 400);
        }

        $user = User::find($verificationToken->user_id);

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé.',
            ], 404);
        }

        $user->update([
            'email_verified_at' => Carbon::now(),
            'is_verified' => true,
        ]);

        $verificationToken->delete();

        return response()->json([
            'message' => 'Email vérifié avec succès.',
        ]);
    }
}