<?php

use BotMan\BotMan\BotMan;
use app\handlers\DefaultHandler;
use app\helpers\DynamicRoutesHelper;
use app\conversations\RemindConversation;
use app\conversations\TimezoneConversation;

$commandRoutes = require_once(__DIR__ . '/dynamicRoutes.php');

$botman->on('new_chat_member', function ($payload, BotMan $bot): void {
    (new DefaultHandler())->chatStarted($bot);
});
$botman->hears('hello', 'app\handlers\DefaultHandler@index');
$botman->hears('/start', 'app\handlers\DefaultHandler@chatStarted');
$botman->hears('/list', 'app\handlers\ListHandler@index');
$botman->hears('/edit_{id}', 'app\handlers\ListHandler@index');
$botman->hears('/remind', function (BotMan $bot): void {
    $bot->startConversation(new RemindConversation());
});
$botman->hears('/timezone', function (BotMan $bot): void {
    $bot->startConversation(new TimezoneConversation());
});
$botman->hears('{sentence}', function ($bot, $sentence) use ($config, $commandRoutes): void {
    DynamicRoutesHelper::process($bot, $sentence, $config, $commandRoutes);
});
$botman->hears('/stop', function ($bot) {
    $bot->reply('stopped');
})->stopsConversation();
