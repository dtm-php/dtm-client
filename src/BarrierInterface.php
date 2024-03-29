<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

interface BarrierInterface
{
    public function call(callable $businessCall): bool;

    public function queryPrepared(string $transType, string $gid): bool;
}
