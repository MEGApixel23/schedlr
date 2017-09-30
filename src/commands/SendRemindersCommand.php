<?php

namespace app\commands;

use Carbon\Carbon;
use app\models\Reminder;
use BotMan\BotMan\BotMan;
use BotMan\Drivers\Telegram\TelegramDriver;

class SendRemindersCommand
{
    public function run(BotMan $botman) : bool
    {
        $query = Reminder::where('when', '<=', Carbon::now()->timestamp)
            ->where('when', '>=', Carbon::now()->subMinutes(30)->timestamp)
            ->where('active', 1)
            ->orderBy('updatedAt', 'asc')
            ->with('chat')
            ->limit(100);

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
                $r->update(['active' => 0]);
            });
        }

        return true;
    }

    private function prepareMessage(Reminder $r) : string
    {
        return "⏰ {$r->what}";
    }

    private function getDriverClass($n) : string
    {
        switch ($n) {
            case 'telegram':
                return TelegramDriver::class;
        }

        return null;
    }
}
