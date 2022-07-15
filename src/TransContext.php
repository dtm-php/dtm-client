<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Util\Str;
use Hyperf\Context\Context;

/**
 * All properties in this class are read-only.
 * All properties data will be stored in the coroutine context.
 */
class TransContext extends Context
{
    use TransOption;

    private static string $gid;

    private static string $transType;

    private static string $dtm;

    private static string $customData;

    /**
     * Use in MSG/SAGA.
     */
    private static array $steps;

    /**
     * Use in MSG/SAGA.
     * @var string[]
     */
    private static array $payloads;

    private static array $binPayLoads;

    /**
     * Use in XA/TCC.
     */
    private static string $branchId;

    /**
     * Use in XA/TCC.
     */
    private static int $subBranchId;

    /**
     * Use in XA/TCC.
     */
    private static string $op;

    /**
     * Use in MSG.
     */
    private static string $queryPrepared;

    private static string $phase2URL = '';

    public static function toArray(): array
    {
        $data = self::getContainer();
        $array = [];
        foreach ($data as $key => $value) {
            if (str_starts_with($key, TransContext::class . '.')) {
                $array[Str::snake(str_replace(TransContext::class . '.', '', $key))] = $value;
            }
        }
        return $array;
    }

    public static function init(string $gid, string $transType, string $branchId)
    {
        static::setGid($gid);
        static::setTransType($transType);
        static::setBranchId($branchId);
    }

    public static function getGid(): string
    {
        return static::get(static::class . '.gid', '');
    }

    public static function setGid(string $gid)
    {
        static::set(static::class . '.gid', $gid);
    }

    public static function getPhase2URL(): string
    {
        return static::get(static::class . '.phase2_url', '');
    }

    public static function setPhase2URL(string $phase2URL)
    {
        static::set(static::class . '.phase2_url', $phase2URL);
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
        return static::get(static::class . '.steps') ?? [];
    }

    public static function setSteps(array $steps)
    {
        static::set(static::class . '.steps', $steps);
    }

    public static function addStep(array $step)
    {
        static::setSteps(array_merge(static::getSteps(), [$step]));
    }

    public static function getPayloads(): array
    {
        return static::get(static::class . '.payloads') ?? [];
    }

    public static function setPayloads(array $payloads)
    {
        static::set(static::class . '.payloads', $payloads);
    }

    public static function addPayload(array $payload)
    {
        static::setPayloads(array_merge(static::getPayloads(), $payload));
    }

    public static function getBinPayLoads(): array
    {
        return static::get(static::class . '.binPayLoads') ?? [];
    }

    public static function setBinPayLoads(array $binPayLoads)
    {
        static::set(static::class . '.binPayLoads', $binPayLoads);
    }

    public static function addBinPayload(array $binPayLoad)
    {
        static::setBinPayLoads(array_merge(static::getBinPayLoads(), $binPayLoad));
    }

    public static function getBranchId(): string
    {
        return static::get(static::class . '.branchId', '');
    }

    public static function setBranchId(string $branchId)
    {
        static::set(static::class . '.branchId', $branchId);
    }

    public static function getSubBranchId(): int
    {
        return static::get(static::class . '.subBranchId', 0);
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
