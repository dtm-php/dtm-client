<?php

namespace DtmClient;


use DtmClient\Exception\DtmException;

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

}