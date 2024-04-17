<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'auth_id',
        'refresh_id',
        'member_id',
        'bitrix_id',
        'active',
        'name',
        'lastname',
        'photo',
        'email',
        'last_login',
        'date_register',
        'is_online',
        'time_zone_offset',
        'timestamp_x',
        'last_activity_date',
        'personal_gender',
        'personal_birthday',
        'user_type',
        'uf_department',
        'lang',
        'portal',
        'auth',
        'member',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array {
        return [
            'uf_department' => 'array',
            'auth' => 'array',
            'member' => 'array',
            'password' => 'hashed',
        ];
    }
}
