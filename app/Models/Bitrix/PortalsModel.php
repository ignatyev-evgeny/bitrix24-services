<?php

namespace App\Models\Bitrix;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PortalsModel extends Model {
    use SoftDeletes;

    protected $table = 'bitrix_portals';

    protected $fillable = [
        'domain',
    ];
}
