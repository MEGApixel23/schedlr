<?php

namespace app\helpers;

class CallHelper
{
    public static function call($callback, $params)
    {
        list($class, $method) = explode('@', $callback);

        return call_user_func_array([new $class(), $method], $params);
    }
}
