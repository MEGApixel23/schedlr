<?php

namespace app\helpers;

function message(string $key, array $params = []): string {
    static $messages = [];

    $locale = $params['locale'] ?? 'en';
    $replacer = $params['replacer'] ?? null;

    if (isset($messages[$locale]) === false) {
        $file = __DIR__ . "/../resources/messages.{$locale}.php";

        if (!file_exists($file)) {
            throw new \Error("No messages file was found for locale {$locale}");
        }

        $messages[$locale] = require_once($file);
    }

    $path = explode('.', $key);
    $res = $messages[$locale];

    foreach ($path as $item) {
        $res = $res[$item];
    }

    return $res;
}
