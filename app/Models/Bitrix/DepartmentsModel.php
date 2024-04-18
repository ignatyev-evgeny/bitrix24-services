<?php

namespace App\Models\Bitrix;

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
    ];
}
