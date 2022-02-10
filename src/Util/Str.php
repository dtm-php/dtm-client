<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Util;

class Str
{
    /**
     * Convert a string to snake case.
     * Code from https://github.com/hyperf/hyperf/blob/master/src/utils/src/Str.php.
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        $key = $value;

        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return $value;
    }

    /**
     * Convert the given string to lower-case.
     * Code from https://github.com/hyperf/hyperf/blob/master/src/utils/src/Str.php.
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * Convert a value to camel case.
     *
     * @param string $value
     * @return string
     */
    public static function camel($value)
    {
        return lcfirst(static::studly($value));
    }

    /**
     * Convert a value to studly caps case.
     */
    public static function studly(string $value, string $gap = ''): string
    {
        $key = $value;

        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', $gap, $value);
    }
}
