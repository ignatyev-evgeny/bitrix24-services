<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UsersGroups extends Model {
    use SoftDeletes;

    protected $fillable = [
        'portal',
        'manager',
        'title',
        'description',
        'tags',
        'users',
    ];

    protected function casts(): array {
        return [
            'tags' => 'array',
            'users' => 'array'
        ];
    }

    public function getManagerInfoAttribute() {
        return User::findOrFail($this->manager);
    }

    public function getManagersFormatAttribute(): string {
        if(empty($this->users)) return __('Не указаны');
        $usersString = "";
        foreach ($this->users as $user) {
            $usersInfo = User::find($user);
            $usersString .= $usersInfo->name."<br>";
        }
        return $usersString;
    }

}
