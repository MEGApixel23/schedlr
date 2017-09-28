<?php

use app\handlers\DefaultHandler;
use app\helpers\DynamicRoutesHelper;

$commandRoutes = require_once(__DIR__ . '/dynamicRoutes.php');

$botman->on('new_chat_member', function ($payload, $bot) {
    (new DefaultHandler())->newMember($bot);
});
$botman->hears('hello', 'app\handlers\DefaultHandler@index');
$botman->hears('{sentence}', function ($bot, $sentence) use ($config, $commandRoutes) {
    DynamicRoutesHelper::process($bot, $sentence, $config, $commandRoutes);
});
