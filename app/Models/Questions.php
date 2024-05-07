<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questions extends Model {
    use SoftDeletes;

    protected $fillable = [
        'portal',
        'title',
        'text',
        'tags',
        'answers',
    ];

    protected function casts() {
        return [
            'answers' => 'array',
            'tags' => 'array'
        ];
    }
}
