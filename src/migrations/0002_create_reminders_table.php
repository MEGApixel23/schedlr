<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

return function () {
    Capsule::schema()
        ->create('reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chatId');
            $table->timestamp('when')->nullable()->default(null);
            $table->string('what');
            $table->string('interval', 8);
            $table->tinyInteger('active')->default(1);
            $table->timestamp('createdAt')->nullable()->default(null);
            $table->timestamp('updatedAt')->nullable()->default(null);
        });
};