<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

/**
 * All properties in this class are read-only.
 * All properties data will be stored in the coroutine context.
 */
trait TransOption
{
    public static bool $waitResult;

    public static int $timeoutToFail;

    public static int $retryInterval;

    /**
     * @var string[]
     */
    public static array $passthroughHeaders = [];

    public static array $branchHeaders = [];

    public static function isWaitResult(): ?bool
    {
        return static::get(TransContext::class . '.waitResult');
    }

    public static function setWaitResult(bool $waitResult)
    {
        static::set(TransContext::class . '.waitResult', $waitResult);
    }

    public static function getTimeoutToFail(): ?int
    {
        return static::get(TransContext::class . '.timeoutToFail');
    }

    public static function setTimeoutToFail(int $timeoutToFail)
    {
        static::set(TransContext::class . '.timeoutToFail', $timeoutToFail);
    }

    public static function getRetryInterval(): ?int
    {
        return static::get(TransContext::class . '.retryInterval');
    }

    public static function setRetryInterval(int $retryInterval)
    {
        static::set(TransContext::class . '.retryInterval', $retryInterval);
    }

    public static function getPassthroughHeaders(): array
    {
        return static::get(TransContext::class . '.passthroughHeaders');
    }

    public static function setPassthroughHeaders(array $passthroughHeaders)
    {
        static::set(TransContext::class . '.passthroughHeaders', $passthroughHeaders);
    }

    public static function getBranchHeaders(): array
    {
        return static::get(TransContext::class . '.branchHeaders');
    }

    public static function setBranchHeaders(array $branchHeaders)
    {
        static::set(TransContext::class . '.branchHeaders', $branchHeaders);
    }
}
