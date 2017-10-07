<?php

namespace app\handlers;

use app\models\Chat;
use app\models\Reminder;
use BotMan\BotMan\BotMan;

class ReminderHandler
{
    public function create(BotMan $bot, array $parts)
    {
        if (isset($parts['when'])) {
            return $this->createFromParts($bot, $parts);
        }

        $when = $this->getDate($parts[0]);
        $what = $this->getWhatToRemind($parts);
        $chatId = $bot->getMessage()->getPayload()['chat']['id'];
        $chat = Chat::where('chatId', $chatId)->first();

        Reminder::create([
            'chatId' => $chat->id,
            'when' => $when,
            'what' => $what,
            'active' => 1
        ]);

        return $bot->reply('👍');
    }

    private function createFromParts(BotMan $bot, array $parts) : Reminder
    {
        $chatId = $bot->getMessage()->getPayload()['chat']['id'];
        $chat = Chat::where('chatId', $chatId)->first();

        return Reminder::create(array_merge($parts, [
            'chatId' => $chat->id
        ]));
    }

    private function getDate(string $str)
    {
        $d = null;
        $date = mb_strtolower($str);

        foreach ($this->getDatesAliases() as $k => $aliases) {
            if ($k === $date || in_array($date, $aliases)) {
                $d = strtotime($k);
                break;
            }
        }

        return $d;
    }

    private function getDatesAliases()
    {
        return [
            'tomorrow' => ['завтра'],
            'today' => ['сегодня']
        ];
    }

    private function getWhatToRemind(array $parts)
    {
        if (count($parts) > 1) {
            return implode(' ', array_slice($parts, 1));
        }

        return 'default string';
    }
}
