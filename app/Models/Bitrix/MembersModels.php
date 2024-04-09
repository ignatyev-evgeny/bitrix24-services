<?php

namespace App\Models\Bitrix;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembersModels extends Model {
    use SoftDeletes;

    protected $table = 'bitrix_members';

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
        'uf_department',
        'current_user',
        'lang',
        'portal',
        'response',
    ];

    protected function casts() {
        return [
            'response' => 'array',
            'uf_department' => 'array',
            'current_user' => 'array',
        ];
    }
}
