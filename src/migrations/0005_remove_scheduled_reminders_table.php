<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

return function () {
    Capsule::schema()
        ->drop('scheduledReminders');
};