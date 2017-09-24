<?php

require_once(__DIR__ . '/../bootstrap.php');

use app\handlers\DefaultHandler;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Telegram\TelegramDriver;

DriverManager::loadDriver(TelegramDriver::class);

$botman = BotManFactory::create($config['botman']);
$botman->hears('hello', DefaultHandler::class . '@index');
$botman->on('new_chat_member', function ($payload, $bot) {
    (new DefaultHandler())->newMember($bot);
});
$botman->listen();
