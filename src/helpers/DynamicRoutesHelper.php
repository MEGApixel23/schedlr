<?php

namespace app\helpers;

class DynamicRoutesHelper
{
    public static function process($bot, $sentence, $config, $commandRoutes)
    {
        $parts = explode(' ', $sentence);
        $normalizedParts = array_map('mb_strtolower', $parts);
        $command = $normalizedParts[0];

        foreach ($config['commandsDictionary'] as $key => $items) {
            $isCurrentCommand = $command === $key || in_array($command, $items);

            if ($isCurrentCommand) {
                CallHelper::call(
                    $commandRoutes[$key],
                    [$bot, array_slice($normalizedParts, 1)]
                );
                break;
            }
        }
    }
}
