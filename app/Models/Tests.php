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
        'attempts',
    ];

    public function getQuestionsCountAttribute(): int {
        return count($this->questions);
    }

    public function getTotalQuestionsScoreAttribute(): int {
        $totalScore = 0;
        foreach ($this->questions as $question) {
            $totalScore += $question['score'];
        }
        return $totalScore;
    }

    public function getFormatTimeMinAttribute(): string {
        return $this->maximum_time > 0 ? floor($this->maximum_time / 60) : 0;
    }

    public function getFormatTimeSecAttribute(): string {
        return $this->maximum_time > 0 ? $this->maximum_time % 60 : 0;
    }

    protected function casts() {
        return [
            'questions' => 'array',
        ];
    }
}
