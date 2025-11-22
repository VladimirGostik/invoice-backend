<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRoleEnum;
use App\Enums\UserStateEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Cache;


class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'state' => UserStateEnum::class,
            'role' => UserRoleEnum::class, // ✅ Cast na enum
        ];
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        // ✅ Bezpečný fallback ak helper neexistuje
        $clientIp = function_exists('get_client_ip')
            ? get_client_ip()
            : (request()->header('X-Real-IP') ?? request()->ip());

        return [
            'ip_address' => $clientIp,
            'role' => $this->role->value,
            'user_id' => $this->id,
            'email' => $this->email,
            'permissions' => ['superadmin'],
        ];
    }

    // ✅ Role helper metódy
    public function isSuperadmin(): bool
    {
        return $this->role === UserRoleEnum::SUPER_ADMIN;
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [
            UserRoleEnum::SUPER_ADMIN,
            UserRoleEnum::ADMIN
        ]);
    }

    public function hasRole(string $role): bool
    {
        return $this->role->value === $role;
    }
}
