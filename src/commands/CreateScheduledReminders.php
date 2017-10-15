<?php

namespace app\commands;

use Carbon\Carbon;
use app\models\Reminder;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class CreateScheduledReminders
{
    private $checkInterval = 60;

    public function run(): bool
    {
        return $this->createQuery()
            ->chunk(100, function (Collection $reminders) {
                $reminders->each(function (Reminder $reminder) {
                    $this->updateNextScheduleDate($reminder);
                });
            });
    }

    private function createQuery(): Builder
    {
        $timeFrom = Carbon::now()->toTimeString();
        $timeTo = Carbon::now()->addMinutes($this->checkInterval)->toTimeString();

        return Reminder::whereRaw('TIME(`when`) BETWEEN ? AND ?', [$timeFrom, $timeTo])
            ->where('active', 1)
            ->orderBy('updatedAt', 'asc');
    }

    private function updateNextScheduleDate(Reminder $reminder): Reminder
    {
        $schedulers = [
            'once' => function (Reminder $reminder): Carbon {
                return Carbon::parse($reminder->when);
            },
            'daily' => function (Reminder $reminder): Carbon {
                if ($reminder->lastSentDate === null) {
                    return Carbon::parse($reminder->when);
                }

                return Carbon::now()->addDay();
            }
        ];
        $next = isset($schedulers[$reminder->interval]) ?
            $schedulers[$reminder->interval]($reminder) :
            null;
        $reminder->update([ 'nextScheduleDate' => $next ]);

        return $reminder;
    }
}
