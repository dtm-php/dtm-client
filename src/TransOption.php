<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

trait TransOption
{
    public bool $waitResult;

    public int $timeoutToFail;

    public int $retryInterval;

    /**
     * @var string[]
     */
    public array $passthroughHeaders = [];

    public array $branchHeaders = [];
}
