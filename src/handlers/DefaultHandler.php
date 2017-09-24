<?php

namespace app\handlers;

use app\models\Chat;
use BotMan\BotMan\BotMan;

class DefaultHandler
{
    public function index(BotMan $bot)
    {
        $bot->reply('Hello there!');
    }

    public function newMember(BotMan $bot)
    {
        $bot->reply('New member!');
        Chat::newChat($bot->getMessage()->getPayload());
    }
}
