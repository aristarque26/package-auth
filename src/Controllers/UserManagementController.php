<?php

namespace Taibi\AuthAPI\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Taibi\AuthAPI\Models\User;

class UserManagementController extends Controller
{
    /**
     * Liste des utilisateurs
     * Super Admin → voit tout le monde
     * Admin → voit personnel + client
     */
    public function index(Request $request)
    {
        $currentUser = $request->user();

        if ($currentUser->isSuperAdmin()) {
            $users = User::all();
        } elseif ($currentUser->isAdmin()) {
            $users = User::whereIn('role', [
                User::ROLE_PERSONNEL,
                User::ROLE_CLIENT,
            ])->get();
        } else {
            return response()->json([
                'message' => 'Accès non autorisé.',
            ], 403);
        }

        return response()->json([
            'message' => 'Liste des utilisateurs',
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $currentUser = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,personnel',
        ]);

        if (!$currentUser->canCreateUser($request->role)) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à créer un utilisateur avec le rôle : ' . $request->role,
                'code' => 'FORBIDDEN_ROLE_CREATION',
                'your_role' => $currentUser->role,
            ], 403);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'message' => 'Utilisateur créé avec succès',
            'user' => $user,
        ], 201);
    }

    public function storeAdmin(Request $request)
    {
        $currentUser = $request->user();

        if (!$currentUser->isSuperAdmin()) {
            return response()->json([
                'message' => 'Seul un Super Admin peut créer un Admin.',
                'code' => 'FORBIDDEN_SUPER_ADMIN_ONLY',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_ADMIN,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'message' => 'Admin créé avec succès',
            'user' => $user,
        ], 201);
    }

    public function storePersonnel(Request $request)
    {
        $currentUser = $request->user();

        if (!$currentUser->isSuperAdmin() && !$currentUser->isAdmin()) {
            return response()->json([
                'message' => 'Seul un Admin ou Super Admin peut créer un Personnel.',
                'code' => 'FORBIDDEN_ROLE',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_PERSONNEL,
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);

        return response()->json([
            'message' => 'Personnel créé avec succès',
            'user' => $user,
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $currentUser = $request->user();
        $user = User::findOrFail($id);

        if (!$currentUser->isSuperAdmin() && !$currentUser->isAdmin()) {
            return response()->json([
                'message' => 'Accès non autorisé.',
            ], 403);
        }

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $currentUser = $request->user();
        $user = User::findOrFail($id);

        if (!$currentUser->canEditUser($user)) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à modifier cet utilisateur.',
                'code' => 'FORBIDDEN_USER_EDIT',
            ], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
        ]);

        $user->update($request->only(['name', 'email']));

        return response()->json([
            'message' => 'Utilisateur modifié avec succès',
            'user' => $user,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $currentUser = $request->user();
        $user = User::findOrFail($id);

        if (!$currentUser->canDeleteUser($user)) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à supprimer cet utilisateur.',
                'code' => 'FORBIDDEN_USER_DELETE',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'Utilisateur supprimé avec succès',
        ]);
    }

    public function toggleStatus(Request $request, $id)
    {
        $currentUser = $request->user();
        $user = User::findOrFail($id);

        if (!$currentUser->canEditUser($user)) {
            return response()->json([
                'message' => 'Vous n\'êtes pas autorisé à modifier cet utilisateur.',
            ], 403);
        }

        $user->is_verified = !$user->is_verified;
        $user->save();

        return response()->json([
            'message' => 'Statut modifié avec succès',
            'user' => $user,
        ]);
    }
}