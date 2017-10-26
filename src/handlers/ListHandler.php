<?php

namespace app\handlers;

use function app\helpers\message;

use Error;
use Carbon\Carbon;
use app\models\Chat;
use app\models\Reminder;
use BotMan\BotMan\BotMan;
use Illuminate\Database\Eloquent\Collection;

class ListHandler
{
    public function index(BotMan $bot)
    {
        $chatId = $bot->getMessage()->getPayload()['chat']['id'];
        $chat = Chat::where('chatId', $chatId)->first();
        $text = $this->getActiveReminders($chat)
            ->map(function ($r) {
                return $this->getReminderDesc($r);
            })
            ->implode("\n");

        $bot->reply($text);
    }

    private function getActiveReminders(Chat $chat): Collection
    {
        return Reminder::where('chatId', $chat->id)->active()->get();
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
            throw new Error(message('invalid_period'));
        }

        return message('reminder_short_desc', [
            '{id}' => $r->id,
            '{date}' => $date,
            '{text}' => $r->what,
        ]);
    }
}
