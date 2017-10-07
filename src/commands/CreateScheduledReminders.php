<?php

namespace app\commands;

use Carbon\Carbon;
use app\models\Reminder;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

class CreateScheduledReminders
{
    public function run() : bool
    {
        $timeTo = Carbon::now()->toTimeString();
        $timeFrom = Carbon::now()->subMinutes(60)->toTimeString();

        /** @var Builder $query */
        $query = Reminder::whereRaw('TIME(`when`) BETWEEN ? AND ?', [ $timeFrom, $timeTo ])
            ->where('active', 1)
            ->orderBy('updatedAt', 'asc');

        $query->chunk(100, function (Collection $reminders) {
            $reminders->each(function (Reminder $reminder) {
                $this->updateNextScheduleDate($reminder);
            });
        });

        return true;
    }

    private function createScheduledReminder(Reminder $reminder)
    {
    }

    private function updateNextScheduleDate(Reminder $reminder)
    {
        $schedulers = [
            'daily' => function () { return Carbon::now()->addDay(); },
            'weekly' => function (Reminder $reminder, Carbon $when) {
                return Carbon::parse("next {$when->format('l')}");
            }
        ];
        $next = isset($schedulers[$reminder->interval]) ?
            $schedulers[$reminder->interval]($reminder, Carbon::parse($reminder->when)) :
            null;

        return $next;
    }
}
