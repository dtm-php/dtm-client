<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use Hyperf\Utils\Context;

class TransContext extends Context
{
    use TransOption;

    protected static string $gid;

    protected static string $transType;

    protected static string $dtm;

    protected static string $customData;

    /**
     * Use in MSG/SAGA.
     */
    protected static array $steps;

    /**
     * Use in MSG/SAGA.
     * @var string[]
     */
    protected static array $payloads;

    protected static array $binPayLoads;

    /**
     * Use in XA/TCC.
     */
    protected static string $branchId;

    /**
     * Use in XA/TCC.
     */
    protected static int $subBranchId;

    /**
     * Use in XA/TCC.
     */
    protected static string $op;

    /**
     * Use in MSG.
     */
    protected static string $queryPrepared;

    public static function init(string $gid, string $transType, string $branchId)
    {
        static::setGid($gid);
        static::setTransType($transType);
        static::setBranchId($branchId);
    }

    public static function getGid(): string
    {
        return static::get(static::class . '.gid');
    }

    public static function setGid(string $gid)
    {
        static::set(static::class . '.gid', $gid);
    }

    public static function getTransType(): string
    {
        return static::get(static::class . '.transType');
    }

    public static function setTransType(string $transType)
    {
        static::set(static::class . '.transType', $transType);
    }

    public static function getDtm(): string
    {
        return static::get(static::class . '.dtm');
    }

    public static function setDtm(string $dtm)
    {
        static::set(static::class . '.dtm', $dtm);
    }

    public static function getCustomData(): string
    {
        return static::get(static::class . '.customData');
    }

    public static function setCustomData(string $customData)
    {
        static::set(static::class . '.customData', $customData);
    }

    public static function getSteps(): array
    {
        return static::get(static::class . '.steps');
    }

    public static function setSteps(array $steps)
    {
        static::set(static::class . '.steps', $steps);
    }

    public static function appendSteps(array $steps)
    {
        static::setSteps(array_merge(static::getSteps(), $steps));
    }

    public static function getPayloads(): array
    {
        return static::get(static::class . '.payloads');
    }

    public static function setPayloads(array $payloads)
    {
        static::set(static::class . '.payloads', $payloads);
    }

    public static function appendPayloads(array $payloads)
    {
        static::setPayloads(array_merge(static::getPayloads(), $payloads));
    }

    public static function getBinPayLoads(): array
    {
        return static::get(static::class . '.binPayLoads');
    }

    public static function setBinPayLoads(array $binPayLoads)
    {
        static::set(static::class . '.binPayLoads', $binPayLoads);
    }

    public static function appendBinPayload(array $binPayLoads)
    {
        static::setBinPayLoads(array_merge(static::getBinPayLoads(), $binPayLoads));
    }

    public static function getBranchId(): string
    {
        return static::get(static::class . '.branchId');
    }

    public static function setBranchId(string $branchId)
    {
        static::set(static::class . '.branchId', $branchId);
    }

    public static function getSubBranchId(): int
    {
        return static::get(static::class . '.subBranchId');
    }

    public static function setSubBranchId(int $subBranchId)
    {
        static::set(static::class . '.subBranchId', $subBranchId);
    }

    public static function getOp(): string
    {
        return static::get(static::class . '.op');
    }

    public static function setOp(string $op)
    {
        static::set(static::class . '.op', $op);
    }

    public static function getQueryPrepared(): string
    {
        return static::get(static::class . '.queryPrepared');
    }

    public static function setQueryPrepared(string $queryPrepared)
    {
        static::set(static::class . '.queryPrepared', $queryPrepared);
    }



}
