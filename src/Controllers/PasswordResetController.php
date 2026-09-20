<?php

namespace Taibi\AuthAPI\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Taibi\AuthAPI\Models\PasswordResetToken;
use Taibi\AuthAPI\Models\User;

class PasswordResetController extends Controller
{
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Email non trouvé.',
            ], 404);
        }

        PasswordResetToken::where('email', $request->email)->delete();

        $token = Str::random(64);
        PasswordResetToken::create([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'message' => 'Email de réinitialisation envoyé.',
            'token' => $token,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetToken = PasswordResetToken::where('email', $request->email)->first();

        if (!$resetToken || !Hash::check($request->token, $resetToken->token)) {
            return response()->json([
                'message' => 'Token invalide.',
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Utilisateur non trouvé.',
            ], 404);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $resetToken->delete();

        return response()->json([
            'message' => 'Mot de passe réinitialisé avec succès.',
        ]);
    }
}  