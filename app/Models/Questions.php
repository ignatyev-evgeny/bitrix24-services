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

    public function getFormatTimeMinAttribute(): string {
        return $this->time > 0 ? floor($this->time / 60) : 0;
    }

    public function getFormatTimeSecAttribute(): string {
        return $this->time > 0 ? $this->time % 60 : 0;
    }

    protected function casts() {
        return [
            'answers' => 'array',
            'tags' => 'array'
        ];
    }
}
