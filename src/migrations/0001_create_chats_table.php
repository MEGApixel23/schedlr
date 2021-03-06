<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as Capsule;

return function () {
    Capsule::schema()
        ->create('chats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('chatId')->unique();
            $table->string('messengerType');
            $table->timestamp('createdAt')->nullable();
            $table->timestamp('updatedAt')->nullable();
        });
};