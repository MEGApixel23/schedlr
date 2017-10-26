<?php

namespace app\handlers;

use function app\helpers\message;

use app\models\Chat;
use app\models\Reminder;
use BotMan\BotMan\BotMan;

class DisableReminderHandler
{
    public function index(BotMan $bot, string $id)
    {
        $chatId = $bot->getMessage()->getPayload()['chat']['id'];
        $reminder = $this->getReminder(
            Chat::where('chatId', $chatId)->first(),
            $id
        );

        if ($reminder && $reminder->dectivate()) {
            $bot->reply(
                message('reminder_disabled', ['{text}' => $reminder->what])
            );
        }
    }

    private function getReminder(Chat $chat, $id): ?Reminder
    {
        return Reminder::active()
            ->where('chatId', $chat->id)
            ->where('id', (int) $id)
            ->first();
    }
}
