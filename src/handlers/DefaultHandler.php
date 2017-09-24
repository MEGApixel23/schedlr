<?php

namespace app\handlers;

use BotMan\BotMan\BotMan;
use Illuminate\Database\Capsule\Manager as Capsule;

class DefaultHandler
{
    public function index(BotMan $bot)
    {
        $bot->reply('Hello there!');
    }

    public function newMember(BotMan $bot)
    {
        $bot->reply('New member!');
        $payload = $bot->getMessage()->getPayload();

        Capsule::table('chats')->insert(['chat_id' => $payload['chat']['id']]);
    }
}
