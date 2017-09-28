<?php

namespace app\handlers;

use BotMan\BotMan\BotMan;

class ReminderHandler
{
    public function create(BotMan $bot, array $parts)
    {
        $date = $this->getDate($parts[0]);

        $bot->reply(date('c', $date));
    }

    private function getDate(string $str)
    {
        $d = null;
        $date = mb_strtolower($str);

        foreach ($this->getDatesAliases() as $k => $aliases) {
            if ($k === $str && in_array($str, $aliases)) {
                $d = date($date);
                break;
            }
        }

        return $d ?: strtotime($date);
    }

    private function getDatesAliases()
    {
        return [
            'tomorrow' => ['завтра'],
            'today' => ['сегодня']
        ];
    }
}
