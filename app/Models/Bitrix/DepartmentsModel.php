<?php

namespace App\Models\Bitrix;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DepartmentsModel extends Model {
    use SoftDeletes;

    protected $table = 'bitrix_departments';

    protected $fillable = [
        'bitrix_id',
        'portal',
        'name',
        'parent',
        'managers',
    ];

    protected function casts(): array {
        return [
            'managers' => 'array',
        ];
    }

    public static function getAllCount(): int {
        return DepartmentsModel::count();
    }

    public function departmentNameByID($departmentID, int $portalID): string {
        return $departmentID ? DepartmentsModel::where('portal', $portalID)->where('bitrix_id', $departmentID)->first()->name : __('Не указан');
    }

}
