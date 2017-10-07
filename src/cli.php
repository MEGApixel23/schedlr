#!/usr/bin/env php
<?php

use app\helpers\CallHelper;

require_once(__DIR__ . '/bootstrap.php');

$commands = [
    'migrate' => 'app\commands\MigrateCommand',
    'remind' => 'app\commands\SendRemindersCommand',
    'schedule' => 'app\commands\CreateScheduledReminders',
];

$cmd = $argv[1] ?? null;
if (isset($commands[$cmd])) {
    $handler = $commands[$cmd];
    CallHelper::call("{$handler}@run", [ $botman ]);
}
