<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const MESSENGER_TYPE_TELEGRAM = 'telegram';

    protected $fillable = [
        'chatId', 'messengerType'
    ];

    public static function newChat($payload, $messengerType = self::MESSENGER_TYPE_TELEGRAM)
    {
        return self::create([
            'chatId' => $payload['chat']['id'],
            'messengerType' => $messengerType
        ]);
    }
}
