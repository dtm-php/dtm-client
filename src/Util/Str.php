<?php

namespace DtmClient\Util;


class Str
{

    /**
     * The cache of snake-cased words.
     */
    protected static $snakeCache = [];

    /**
     * Convert a string to snake case.
     * Code from https://github.com/hyperf/hyperf/blob/master/src/utils/src/Str.php
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        $key = $value;

        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }

        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return static::$snakeCache[$key][$delimiter] = $value;
    }

    /**
     * Convert the given string to lower-case.
     * Code from https://github.com/hyperf/hyperf/blob/master/src/utils/src/Str.php
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

}