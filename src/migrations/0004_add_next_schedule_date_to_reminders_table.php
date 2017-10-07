<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

return function () {
    Capsule::schema()
        ->table('reminders', function (Blueprint $table) {
            $table->date('nextScheduleDate')->nullable()->default(null);
        });
};