<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Constants\Operation;
use DtmClient\Constants\Branch;
use Hyperf\Utils\Context;
use DtmClient\Exception\DtmException;
use Hyperf\DB\DB as SimpleDB;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Contract\RequestInterface;

class MySqlBarrier implements BarrierInterface
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
            return 0;
        }

        if (static::hasSimpleDb()) {
            return SimpleDB::execute(
                'INSERT IGNORE INTO `barrier` (trans_type, gid, branch_id, op, barrier_id, reason) values(?,?,?,?,?,?)',
                [$transType, $gid, $branchId, $op, $barrierID, $reason]
            );
        } else {
            return Db::table('barrier')->insertOrIgnore([
                'trans_type' => $transType,
                'gid' => $gid,
                'branch_id' => $branchId,
                'op' => $op,
                'barrier_id' => $barrierID,
                'reason' => $reason
            ]);
        }
    }

    public static function call()
    {
        $gid =  TransContext::getGid();
        $branchId = TransContext::getBranchId();
        $transType = TransContext::getTransType();
        $op = TransContext::getOp();

        static::$barrierId++;
        $barrierID = static::$barrierId;
        $bid = sprintf('%02d', $barrierID);

        $originOP = static::$opMap[$op] ?? '';

        static::hasSimpleDb() ? SimpleDB::beginTransaction() : Db::beginTransaction();


        try {
            $originAffected = MySqlBarrier::insertBarrier($transType, $gid, $branchId, $originOP, $bid, $op);
            $currentAffected = MySqlBarrier::insertBarrier($transType, $gid, $branchId, $op, $bid, $op);
            static::hasSimpleDb() ? SimpleDB::rollback() : Db::rollback();

            if (
                ($op == Operation::BRANCH_CANCEL || $op == Operation::BRANCH_COMPENSATE) // null compensate
                && $originAffected > 0 // repeated request or dangled request
            ) {
                $currentAffected = 0;
                return true;
            }

            return true;
        } catch (\Throwable $throwable) {
            static::hasSimpleDb() ? SimpleDB::commit() : Db::commit();
            throw $throwable;
        }
    }

    public static function hasSimpleDb()
    {
        return class_exists(SimpleDB::class);
    }

}
