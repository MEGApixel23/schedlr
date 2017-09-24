<?php

return [
    'appName' => 'schedlr',
    'botman' => [
        'telegram' => [
            'token' => getenv('TELEGRAM_TOKEN')
        ]
    ],
    'db' => [
        'driver' => 'mysql',
        'host' => getenv('MYSQL_HOST'),
        'database' => getenv('MYSQL_DB'),
        'username' => getenv('MYSQL_USER'),
        'password' => getenv('MYSQL_PASS'),
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
    ]
];
