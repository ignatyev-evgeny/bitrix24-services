<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questions extends Model {
    use SoftDeletes;

    protected $fillable = [
        'portal',
        'time',
        'title',
        'text',
        'tags',
        'answers',
    ];

    public function getFormatTimeAttribute(): string {
        return $this->time > 0 ? floor($this->time / 60)." : ".$this->time % 60 : __('Неограниченно');
    }

    protected function casts() {
        return [
            'answers' => 'array',
            'tags' => 'array'
        ];
    }
}
