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

        return Reminder::timeBetween($timeFrom, $timeTo)
            ->active()
            ->notScheduled()
            ->notSentDuring(Carbon::now()->subHour(2))
            ->orderBy('updatedAt', 'asc');
    }

    private function updateNextScheduleDate(Reminder $reminder): Reminder
    {
        $schedulers = [
            'once' => function (Reminder $reminder): Carbon {
                return Carbon::parse($reminder->when);
            },
            'daily' => function (Reminder $reminder): Carbon {
                $d = Carbon::parse($reminder->when);
                $now = Carbon::now();
                $isTimePassed = $d->hour === $now->hour && $d->minute < $now->minute || $d->hour < $now->hour;

                if ($isTimePassed) {
                    $now->addDay();
                }

                return $now->hour($d->hour)
                    ->minute($d->minute);
            }
        ];
        $next = isset($schedulers[$reminder->interval]) ?
            $schedulers[$reminder->interval]($reminder) :
            null;
        $reminder->update(['nextScheduleDate' => $next]);

        return $reminder;
    }
}
