<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Collection;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    // protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'gender',
        'phone',
        'address',
        'password',
        'userable_id',
        'userable_type',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

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
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function userable()
    {
        return $this->morphTo();
    }

    public function getUserTypeAttribute()
    {
        if ($this->userable_type) {
            return strtolower(class_basename($this->userable_type));
        }
        return 'super_admin';
    }

    public function getGuardNames(): Collection
    {
        if ($this->getUserTypeAttribute() === 'super_admin') {
            return collect(['web', 'api']);
        }

        return collect(['api']);
    }

    public function assignRoleWithGuard($roleName)
    {
        $guardName =
            $this->getUserTypeAttribute() === 'super_admin' ? 'web' : 'api';

        $role = \Spatie\Permission\Models\Role::where('name', $roleName)
            ->where('guard_name', $guardName)
            ->first();

        if ($role) {
            $this->assignRole($role);
        }
    }

    public function hasRoleWithGuard($roleName)
    {
        $guardName =
            $this->getUserTypeAttribute() === 'super_admin' ? 'web' : 'api';
        return $this->hasRole($roleName, $guardName);
    }
}
