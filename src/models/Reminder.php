<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Chat $chat
 * @property string $what
 * @property string $interval
 */
class Reminder extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'chatId', 'when', 'what',
        'interval', 'active', 'nextScheduleDate'
    ];

    public function chat()
    {
        return $this->hasOne(Chat::class, 'id', 'chatId');
    }
}
