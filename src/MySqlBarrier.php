<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Constants\Operation;
use Hyperf\Utils\Context;
use DtmClient\Exception\DtmException;
use Hyperf\DB\DB as SimpleDB;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Contract\RequestInterface;

class MySqlBarrier implements BarrierInterface
{
    protected static int $barrierId = 0;

    protected static $opMap = [
        'cancel' => 'try',
        'compensate' => 'action'
    ];

    public static function barrierFrom(string $transType, string $gid, string $branchId, string $op)
    {
        $branchBarrier = new BranchBarrier();
        $branchBarrier->transType = $transType;
        $branchBarrier->gid = $gid;
        $branchBarrier->branchId = $branchId;
        $branchBarrier->op = $op;
        if (! $branchBarrier->isValid()) {
            throw new DtmException(sprintf('Invalid transaction info: %s', $branchBarrier));
        }
        return $branchBarrier;
    }

    public static function insertBarrier(string $transType, string $gid, string $branchId, string $op, string $barrierID, string $reason)
    {
        if (empty($op)) {
            return 0;
        }
        $sql = 'INSERT IGNORE INTO `barrier` (trans_type, gid, branch_id, op, barrier_id, reason) values(?,?,?,?,?,?)';
        $bindings = [$transType, $gid, $branchId, $op, $barrierID, $reason];

        if (static::hasSimpleDb()) {
            return SimpleDB::execute($sql, $bindings);
        } else {
            return Db::execute($sql, $bindings);
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
