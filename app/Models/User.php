<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Bitrix\DepartmentsModel;
use App\Models\Bitrix\PortalsModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @method static whereBitrixId(mixed $ID)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'auth_id',
        'refresh_id',
        'member_id',
        'bitrix_id',
        'active',
        'is_admin',
        'is_support',
        'is_manager',
        'active',
        'name',
        'photo',
        'email',
        'phone_personal',
        'phone_work',
        'departments',
        'position',
        'lang',
        'portal',
        'auth',
        'user',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array {
        return [
            'departments' => 'array',
            'auth' => 'array',
            'user' => 'array',
            'password' => 'hashed',
            'active' => 'boolean',
            'is_admin' => 'boolean',
            'is_support' => 'boolean',
            'is_manager' => 'boolean',
        ];
    }

    public function portalObject(): HasOne {
        return $this->hasOne(PortalsModel::class, 'id', 'portal');
    }

    public static function getAllCount(): int {
        return User::count();
    }

    public function departments(): array {
        $departmentsArray = [];
        foreach ($this->departments as $department) {
            $departmentObject = DepartmentsModel::where('portal', $this->portal)->where('bitrix_id', $department)->first();
            $departmentsArray[] = $departmentObject->name;
        }
        return $departmentsArray;
    }

    public function getEmailFormatAttribute(): ?string {
        return !empty($this->email) ? "<a href='mailto:$this->email'>$this->email</a>" : "<b>Не указан</b>";
    }

    public function getPhotoFormatAttribute(): ?string {
        return !empty($this->photo) ? $this->photo : asset('/img/default-150x150.png');
    }

    public function getIntActiveAttribute(): ?string {
        return $this->active ? 1 : 0;
    }

    public function getIntIsSupportAttribute(): ?string {
        return $this->is_support ? 1 : 0;
    }

    public static function checkManagerInDepartment(int $departmentId, int $managerId): bool {
        return DepartmentsModel::whereId($departmentId)->whereJsonContains('managers', "$managerId")->exists();
    }

    public function getBitrixProfileLinkAttribute(): ?string {
        return "<a target='_blank' href='https://{$this->portalObject->domain}/company/personal/user/{$this->bitrix_id}/'>$this->bitrix_id</a>";
    }
}
