<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    protected $fillable = [
        'group_id',
        'lesson_date',
        'day_of_week',
        'start_time',
        'end_time',
        'subject',
        'classroom',
        'week_number',
        'topic',
        'objectives',
        'materials',
        'homework',
        'notes',
        'is_completed',
    ];

    protected $casts = [
        'lesson_date' => 'date',
        'start_time' => 'string',
        'end_time' => 'string',
        'is_completed' => 'boolean',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->lesson_date ? $this->lesson_date->format('d.m.Y') : '';
    }

    public function getStatusAttribute(): string
    {
        return $this->is_completed ? 'Проведен' : 'Запланирован';
    }
}
