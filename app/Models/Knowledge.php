<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Knowledge extends Model {
    use SoftDeletes;

    protected $fillable = [
        'portal',
        'title',
        'description',
        'tags',
        'questions',
        'tests',
    ];

    protected function casts() {
        return [
            'tags' => 'array',
            'questions' => 'array',
            'tests' => 'array',
        ];
    }
}
