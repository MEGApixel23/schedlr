<?php

require_once(__DIR__ . '/../bootstrap.php');

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Telegram\TelegramDriver;

DriverManager::loadDriver(TelegramDriver::class);

$botman = BotManFactory::create($config['botman']);
$botman->hears('hello', function (BotMan $bot) {
    $bot->reply('Hello yourself.');
});

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $botman->say(
        '/test', '-224539993', TelegramDriver::class
    );
}



// start listening
//$botman->listen();