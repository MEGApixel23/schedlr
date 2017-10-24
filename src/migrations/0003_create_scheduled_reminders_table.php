<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

return function () {
    Capsule::schema()
        ->create('scheduledReminders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chatId');
            $table->timestamp('when')->nullable()->default(null);
            $table->string('what');
            $table->timestamp('createdAt')->nullable()->default(null);
            $table->timestamp('updatedAt')->nullable()->default(null);
        });
};