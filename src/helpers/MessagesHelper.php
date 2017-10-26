<?php

namespace app\helpers;

function message(string $key, ...$params): string {
    static $messages = [];

    $locale = 'en';
    $replaces = null;

    foreach ($params as $param) {
        if (is_string($param)) {
            $locale = $param;
        } elseif (is_array($param)) {
            $replaces = $param;
        }
    }

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

    if ($replaces) {
        foreach ($replaces as $placeholder => $replace) {
            $res = str_replace($placeholder, $replace, $res);
        }
    }

    return $res;
}
