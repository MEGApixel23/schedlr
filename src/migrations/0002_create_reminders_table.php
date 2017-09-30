<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

return function () {
    Capsule::schema()
        ->create('reminders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chatId');
            $table->string('when');
            $table->string('what');
            $table->tinyInteger('active')->default(1);
            $table->timestamp('createdAt')->nullable();
            $table->timestamp('updatedAt')->nullable();
        });
};