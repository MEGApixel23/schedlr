<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

return function () {
    Capsule::schema()
        ->table('chats', function (Blueprint $table) {
            $table->tinyInteger('timezone')->default(0);
        });
};