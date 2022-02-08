<?php

namespace DtmClient;


use DtmClient\Exception\DtmException;
use Hyperf\DB\DB;

class Barrier
{

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
            return null;
        }

        DB::insert(
            'INSERT IGNORE INTO `barrier` (trans_type, gid, branch_id, op, barrier_id, reason) values(?,?,?,?,?,?)',
            [$transType, $gid, $branchId, $op, $barrierID, $reason]
        );
    }

    public static function call(callable $callback)
    {
        $barrierID = 1;

        $bid = sprintf('%02d', $barrierID);
    }

    

}