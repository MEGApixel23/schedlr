<?php

namespace app\handlers;

use function app\helpers\message;

use Carbon\Carbon;
use app\models\Chat;
use app\models\Reminder;
use BotMan\BotMan\BotMan;

class ListHandler
{
    public function index(BotMan $bot)
    {
        $chatId = $bot->getMessage()->getPayload()['chat']['id'];
        $chat = Chat::where('chatId', $chatId)->first();
        $reminders = Reminder::where('chatId', $chat->id)
            ->active()
            ->get();

        $bot->reply(
            $reminders->map(function ($r) {
                return $this->getReminderDesc($r);
            })->implode("\n")
        );
    }

    private function getReminderDesc(Reminder $r): string
    {
        $c = Carbon::parse($r->when);
        if ($r->interval === 'once') {
            $date = $c->format(message('once_date_format'));
        } elseif ($r->interval === 'daily') {
            $f = $c->format(message('daily_date_format'));
            $interval = message('interval.daily');
            $date = "{$interval} {$f}";
        } else {
            throw new \Error(message('invalid_period'));
        }

        return "{$date} \"{$r->what}\" /edit_{$r->id}";
    }
}
