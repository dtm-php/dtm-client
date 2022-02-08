<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Exception\DtmException;
use Hyperf\DB\DB;
use Psr\Http\Message\RequestInterface;

class Barrier
{
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
            return null;
        }

        DB::insert(
            'INSERT IGNORE INTO `barrier` (trans_type, gid, branch_id, op, barrier_id, reason) values(?,?,?,?,?,?)',
            [$transType, $gid, $branchId, $op, $barrierID, $reason]
        );
    }

    public static function call(callable $callback)
    {
        /** @var RequestInterface $request */
        $request = Context::get(RequestInterface::class);

        $inputs = $request->all();

        $op = $inputs[0]['op'] ?? $inputs['op'];
        $gid = $inputs[0]['gid']?? $inputs['gid'];
        $branchId = $inputs[0]['branch_id']?? $inputs['branch_id'];
        $transType = $inputs[0]['trans_type']?? $inputs['trans_type'];
        TransContext::setGid();
        TransContext::setBranchId($branchId);
        TransContext::setTransType($transType);
        TransContext::setOp($op);

        TransContext::setBarrierID(TransContext::getBarrierID() + 1);
        $barrierID = TransContext::getBarrierID();
        $bid = sprintf('%02d', $barrierID);

        $originOP = static::$opMap[$op] ?? '';
        DB::beginTransaction();
        try {
            \DtmClient\Barrier::insertBarrier($transType, $gid, $branchId, $originOP, $bid, $barrierID);
            \DtmClient\Barrier::insertBarrier($transType, $gid, $branchId, $op, $bid, $barrierID);
            $result = $callback();
            DB::commit();
            return $result;
        } catch (\Throwable $throwable) {
            DB::rollback();
            throw $throwable;
        }
    }
}
