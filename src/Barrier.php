<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Constants\Branch;
use Hyperf\Utils\Context;
use DtmClient\Exception\DtmException;
use Hyperf\DB\DB;
use Hyperf\HttpServer\Contract\RequestInterface;

class Barrier
{
    protected static int $barrierId = 0;

    protected static $opMap = [
        Branch::BranchCancel => Branch::BranchTry,
        Branch::BranchCompensate => Branch::BranchAction
    ];

    public static function barrierFrom(string $transType, string $gid, string $branchId, string $op)
    {
        TransContext::setTransType($transType);
        TransContext::setGid($gid);
        TransContext::setBranchId($branchId);
        TransContext::setOp($op);
        if (! TransContext::getTransType() || ! TransContext::getGid() || ! TransContext::getBranchId() || ! TransContext::getOp()) {
            throw new DtmException(sprintf('Invalid transaction info: %s', $branchBarrier));
        }
    }

    public static function insertBarrier(string $transType, string $gid, string $branchId, string $op, string $barrierID, string $reason)
    {
        if (empty($op)) {
            return null;
        }

        DB::insert(
            'INSERT IGNORE INTO `barrier` (trans_type, gid, branch_id, op, barrier_id, reason) values(?,?,?,?,?,?)',
            [$transType, $gid, $branchId, $op, $barrierID, $reason]
        );
    }

    public static function call(callable $callback)
    {
        $gid =  TransContext::getGid();
        $branchId = TransContext::getBranchId();
        $transType = TransContext::getTransType();
        $op = TransContext::getOp();

        static::$barrierId++;
        $barrierID = static::$barrierId;
        $bid = sprintf('%02d', $barrierID);

        $originOP = static::$opMap[$op] ?? '';
        DB::beginTransaction();
        try {
            \DtmClient\Barrier::insertBarrier($transType, $gid, $branchId, $originOP, $bid, $op);
            \DtmClient\Barrier::insertBarrier($transType, $gid, $branchId, $op, $bid, $op);
            $result = $callback();
            DB::commit();
            return $result;
        } catch (\Throwable $throwable) {
            DB::rollback();
            throw $throwable;
        }
    }
}
