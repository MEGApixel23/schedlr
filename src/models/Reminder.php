<?php

namespace app\models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = [
        'chatId', 'when', 'what',
        'active'
    ];
}
