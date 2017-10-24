<?php

namespace app\handlers;

use app\models\Chat;
use BotMan\BotMan\BotMan;
use app\conversations\RemindConversation;

class DefaultHandler
{
    public function index(BotMan $bot)
    {
        $bot->reply('Hello there!');
    }

    public function chatStarted(Botman $bot): void
    {
        Chat::newChat($bot->getMessage()->getPayload());

        $bot->startConversation(new RemindConversation());
    }
}
