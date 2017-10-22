<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property Chat $chat
 * @property string $what
 * @property string $when
 * @property string $interval
 * @property string $lastSentDate
 * @property string $nextScheduleDate
 */
class Reminder extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'chatId', 'when', 'what',
        'interval', 'active', 'nextScheduleDate',
        'lastSentDate'
    ];

    public function chat()
    {
        return $this->hasOne(Chat::class, 'id', 'chatId');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }
}
