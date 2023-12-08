<?php

namespace Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    public const GROUP_OPERATORS = 'operators';
    public const GROUP_CUSTOMERS = 'customers';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_EDITOR = 'editor';
    public const ROLE_CUSTOMER = 'customer';
    public const ROLE_AUTH = 'auth';

    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'init',
        'email',
        'password',
        'group',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'roles'  => 'json',
        'meta'  => 'json',
    ];
    
    public function hasRole($rid)
    {
        if ($rid === self::ROLE_AUTH) return true;
        if ($this->isAdmin()) return true;
        if (in_array($rid, $this->roles)) return true;
        switch ($rid) {
            case self::ROLE_CUSTOMER:
                return in_array(self::ROLE_EDITOR, $this->roles);
        }
        return false;
    }

    public function isAdmin()
    {
        return $this->id == 1 || in_array(self::ROLE_ADMIN, $this->roles);
    }

}
