<?php

namespace app\commands;

use Carbon\Carbon;
use app\models\Reminder;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder;

class CreateScheduledReminders
{
    private $checkInterval = 60;

    public function run() : bool
    {
        $timeFrom = Carbon::now()->toTimeString();
        $timeTo = Carbon::now()->addMinutes(60)->toTimeString();

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

    private function updateNextScheduleDate(Reminder $reminder) : Carbon
    {
        $schedulers = [
            'daily' => function (Reminder $reminder) {
                if ($reminder->lastSentDate === null) {
                    return Carbon::parse($reminder->when);
                }

                return Carbon::now()->addDay();
            }
        ];
        $next = isset($schedulers[$reminder->interval]) ?
            $schedulers[$reminder->interval]($reminder) :
            null;

        $reminder->update([
            'nextScheduleDate' => $next
        ]);

        return $next;
    }
}
