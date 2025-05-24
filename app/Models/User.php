<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use BinaryCabin\LaravelUUID\Traits\HasUUID;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\Relationships\UserRelations;
use App\Traits\Attributes\UserAttributes;


class User extends Authenticatable
{
    use HasApiTokens,
        HasFactory,
        Notifiable,
        HasRoles,
        HasUUID,
        SoftDeletes,
        UserAttributes,
        UserRelations;

    protected $appends = [
        'full_name',
        'role_name',
        'short_name',
        'total_time_logged',
        'remaining_leaves'
    ];

    const SUPER_ADMIN_EMAIL = 'superadmin@projectx.com';

    const STATUS_ACTIVE = 'active';
    const STATUS_IN_ACTIVE = 'in-active';
    const STATUS_PENDING = 'pending';
    const STATUS_BLOCK = 'block';

    const ROLE_SUPERADMIN = 'super-admin';
    const ROLE_ADMIN = 'admin';
    const ROLE_DESIGNER = 'designer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'company_id',
        'first_name',
        'last_name',
        'phone_number',
        'profile_picture',
        'timezone',
        'status',
        'location',
        'joining_date',
        'leaving_date',
        'can_procure',
        'hourly_rate',
        'sick_leaves',
        'casual_leaves',
        'annual_leaves',
        ''
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
    ];

    public function sendPasswordResetNotification($token): void
    { // set custom notification on reset notification
        $this->notify(new ResetPasswordNotification($token));
    }
    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->password)) {
                $user->password = bcrypt('password');
            }
        });
    }

    public static function findOrFailByUuid(string $uuid)
    {
        return static::byUUID($uuid)->firstOrFail();
    }
}
