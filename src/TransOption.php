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
        return self::$waitResult ?? null;
    }

    public static function setWaitResult(bool $waitResult)
    {
        self::$waitResult = $waitResult;
    }

    public static function getTimeoutToFail(): ?int
    {
        return self::$timeoutToFail ?? null;
    }

    public static function setTimeoutToFail(int $timeoutToFail)
    {
        self::$timeoutToFail = $timeoutToFail;
    }

    public static function getRetryInterval(): ?int
    {
        return self::$retryInterval ?? null;
    }

    public static function setRetryInterval(int $retryInterval)
    {
        self::$retryInterval = $retryInterval;
    }

    public static function getPassthroughHeaders(): array
    {
        return self::$passthroughHeaders;
    }

    public static function setPassthroughHeaders(array $passthroughHeaders)
    {
        self::$passthroughHeaders = $passthroughHeaders;
    }

    public static function getBranchHeaders(): array
    {
        return self::$branchHeaders;
    }

    public static function setBranchHeaders(array $branchHeaders)
    {
        self::$branchHeaders = $branchHeaders;
    }

}
