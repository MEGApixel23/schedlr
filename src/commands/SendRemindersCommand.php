<?php

namespace app\commands;

use Carbon\Carbon;
use app\models\Reminder;
use BotMan\BotMan\BotMan;
use Illuminate\Database\Eloquent\Builder;
use BotMan\Drivers\Telegram\TelegramDriver;

class SendRemindersCommand
{
    public function run(BotMan $botman): bool
    {
        $query = $this->createQuery();

        while (true) {
            $reminders = $query->get();

            if ($reminders->isEmpty()) {
                break;
            }

            $reminders->map(function (Reminder $r) use ($botman) {
                $botman->say(
                    $this->prepareMessage($r),
                    $r->chat->chatId,
                    $this->getDriverClass($r->chat->messengerType)
                );
                $this->markSent($r);
            });
        }

        return true;
    }

    private function createQuery(): Builder
    {
        $from = Carbon::now();
        $to = Carbon::now()->addMinutes(30);

        return Reminder::whereBetween('nextScheduleDate', [$from, $to])
            ->where('active', 1)
            ->orderBy('nextScheduleDate', 'asc')
            ->with('chat')
            ->limit(100);
    }

    private function prepareMessage(Reminder $r): string
    {
        return $r->what;
    }

    private function getDriverClass($n): string
    {
        switch ($n) {
            case 'telegram':
                return TelegramDriver::class;
        }

        return null;
    }

    private function markSent(Reminder $r): Reminder
    {
        $periods = [
            'once' => function (): array {
                return [
                    'active' => 0,
                    'lastSentDate' => Carbon::now()->toIso8601String()
                ];
            },
            'daily' => function (): array {
                return [
                    'lastSentDate' => Carbon::now()->toIso8601String()
                ];
            },
        ];

        if (isset($periods[$r->interval])) {
            $r->update($periods[$r->interval]());
        }

        return $r;
    }
}
