<?php

require_once(__DIR__ . '/vendor/autoload.php');

use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;

(new Dotenv(__DIR__))->load();

$config = require_once(__DIR__ . '/config.php');

$capsule = new Capsule();
$capsule->addConnection($config['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();
