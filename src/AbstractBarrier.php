<?php

namespace DtmClient;

use Hyperf\DB\DB as SimpleDB;

abstract class AbstractBarrier implements BarrierInterface
{
    protected function hasSimpleDb(): bool
    {
        return class_exists(SimpleDB::class);
    }
}