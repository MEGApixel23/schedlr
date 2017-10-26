<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|int $chatId
 * @property string $messengerType
 */
class Chat extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const MESSENGER_TYPE_TELEGRAM = 'telegram';

    protected $fillable = [
        'chatId', 'messengerType', 'timezone'
    ];

    public static function newChat($payload, $messengerType = self::MESSENGER_TYPE_TELEGRAM): self
    {
        $attr = [
            'chatId' => $payload['chat']['id'],
            'messengerType' => $messengerType
        ];
        $i = self::where($attr)->first();

        return $i ?: self::create([
            'chatId' => $payload['chat']['id'],
            'messengerType' => $messengerType
        ]);
    }
}
