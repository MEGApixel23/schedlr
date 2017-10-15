<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Chat $chat
 * @property string $what
 * @property string $interval
 * @property string $when
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
}
