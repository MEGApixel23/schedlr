<?php

namespace app\models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property Chat $chat
 * @property string $what
 * @property string $when
 * @property string $interval
 * @property string $lastSentDate
 * @property string $nextScheduleDate
 */
class Reminder extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'chatId', 'when', 'what',
        'interval', 'active', 'nextScheduleDate',
        'lastSentDate'
    ];

    public function chat()
    {
        return $this->hasOne(Chat::class, 'id', 'chatId');
    }

    public function scopeTimeBetween(Builder $query, string $timeFrom, string $timeTo): Builder
    {
        return $query->whereRaw('TIME(`when`) BETWEEN ? AND ?', [$timeFrom, $timeTo]);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', 1);
    }

    public function scopeNotScheduled(Builder $query): Builder
    {
        return $query->whereNull('nextScheduleDate');
    }

    public function scopeNotSentDuring(Builder $query, Carbon $during): Builder
    {
        return $query->where('lastSentDate', '<=', $during->toIso8601String())
            ->orWhereNull('lastSentDate');
    }
}
