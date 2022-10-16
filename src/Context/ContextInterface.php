<?php

namespace DtmClient\Context;

interface ContextInterface
{
    public static function set(string $id, $value);

    public static function get(string $id, $default = null, $coroutineId = null);

    public static function getContainer();
}