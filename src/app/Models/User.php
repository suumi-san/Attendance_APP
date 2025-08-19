<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';


    public static array $roles = [
        self::ROLE_ADMIN => '管理者',
        self::ROLE_USER => '一般ユーザー',
    ];


    protected $fillable = [
        'name',
        'email',
        'password',
        'registered_at',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'registered_at' => 'datetime',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function correctionRequests()
    {
        return $this->hasManyThrough(
            CorrectionRequest::class,
            Attendance::class,
            'user_id',
            'attendance_id',
            'id',
            'id'
        );
    }

    /**
     * ロール判定ヘルパー
     */
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }
}
