<?php

namespace Taibi\AuthAPI\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_CLIENT = 'client';
    const ROLE_PERSONNEL = 'personnel';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_verified' => 'boolean',
    ];

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isClient(): bool
    {
        return $this->role === self::ROLE_CLIENT;
    }

    public function isPersonnel(): bool
    {
        return $this->role === self::ROLE_PERSONNEL;
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function canCreateUser(string $targetRole): bool
    {
        if ($this->isSuperAdmin()) {
            return in_array($targetRole, [self::ROLE_ADMIN, self::ROLE_PERSONNEL]);
        }
        if ($this->isAdmin()) {
            return $targetRole === self::ROLE_PERSONNEL;
        }
        return false;
    }

    public function canEditUser(User $targetUser): bool
    {
        if ($this->isSuperAdmin()) {
            return $this->id !== $targetUser->id;
        }
        if ($this->isAdmin()) {
            return in_array($targetUser->role, [self::ROLE_PERSONNEL, self::ROLE_CLIENT]);
        }
        return false;
    }

    public function canDeleteUser(User $targetUser): bool
    {
        if ($this->isSuperAdmin()) {
            return $this->id !== $targetUser->id;
        }
        if ($this->isAdmin()) {
            return $targetUser->role === self::ROLE_PERSONNEL;
        }
        return false;
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->is_verified && !is_null($this->email_verified_at);
    }
}