<?php

require_once(__DIR__ . '/vendor/autoload.php');

use Dotenv\Dotenv;
use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Cache\RedisCache;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Telegram\TelegramDriver;
use Illuminate\Database\Capsule\Manager as Capsule;

(new Dotenv(__DIR__))->load();

$config = require_once(__DIR__ . '/config.php');

$capsule = new Capsule();
$capsule->addConnection($config['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

DriverManager::loadDriver(TelegramDriver::class);
$botman = BotManFactory::create($config['botman'], new RedisCache('cache', 6379));

require_once(__DIR__ . '/routes/routes.php');
