<?php

namespace DtmClient\Context;

use Hyperf\Context\Context as HyperfContext;

class Context implements ContextInterface
{
    protected static array $nonCoContext = [];

    public static function set(string $id, $value)
    {
        if (static::isUseCoroutineExtension()) {
            return HyperfContext::set($id, $value);
        }

        static::$nonCoContext[$id] = $value;
        return $value;
    }

    public static function get(string $id, $default = null, $coroutineId = null)
    {
        if (static::isUseCoroutineExtension()) {
            return HyperfContext::get($id, $default, $coroutineId);
        }

        return static::$nonCoContext[$id] ?? $default;
    }

    public static function getContainer()
    {
        if (static::isUseCoroutineExtension()) {
            return HyperfContext::getContainer();
        }

        return static::$nonCoContext;
    }


    private static function isUseCoroutineExtension(): bool
    {
        return extension_loaded('Swow') || extension_loaded('Swoole');
    }

}