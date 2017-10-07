<?php

use app\handlers\DefaultHandler;
use app\helpers\DynamicRoutesHelper;

$commandRoutes = require_once(__DIR__ . '/dynamicRoutes.php');

$botman->on('new_chat_member', function ($payload, $bot) {
    (new DefaultHandler())->chatStarted($bot);
});
$botman->hears('hello', 'app\handlers\DefaultHandler@index');
$botman->hears('/start', 'app\handlers\DefaultHandler@chatStarted');
$botman->hears('/remind', function ($bot) {
    return $bot->startConversation(new \app\conversations\RemindConversation());
});
$botman->hears('{sentence}', function ($bot, $sentence) use ($config, $commandRoutes) {
    DynamicRoutesHelper::process($bot, $sentence, $config, $commandRoutes);
});
$botman->hears('/stop', function ($bot) {
    $bot->reply('stopped');
})->stopsConversation();
