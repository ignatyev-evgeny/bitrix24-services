<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tests extends Model {
    use SoftDeletes;

    protected $fillable = [
        'portal',
        'title',
        'description',
        'maximum_score',
        'passing_score',
        'skipping',
        'ranging',
        'questions',
        'maximum_time',
    ];

    public function getQuestionsCountAttribute(): int {
        return count($this->questions);
    }

    protected function casts() {
        return [
            'questions' => 'array',
        ];
    }
}
