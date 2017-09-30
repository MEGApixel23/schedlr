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

    public function chatStarted(Botman $bot) : Chat
    {
        return Chat::newChat(
            $bot->getMessage()->getPayload()
        );
    }
}
