<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Constants\Branch;
use DtmClient\Constants\Operation;
use DtmClient\Constants\TransType;
use DtmClient\DbTransaction\DBTransactionInterface;
use DtmClient\Exception\DuplicatedException;

class MySqlBarrier implements BarrierInterface
{
    protected DBTransactionInterface $DBTransaction;

    public function __construct(DBTransactionInterface $DBTransaction)
    {
        $this->DBTransaction = $DBTransaction;
    }

    public function call(callable $businessCall): bool
    {
        $gid = TransContext::getGid();
        $branchId = TransContext::getBranchId();
        $transType = TransContext::getTransType();
        $op = TransContext::getOp();

        $barrierID = TransContext::getBarrierId() + 1;
        TransContext::setBarrierId($barrierID);

        $bid = sprintf('%02d', $barrierID);

        $originOP = [
            Branch::BranchCancel => Branch::BranchTry,
            Branch::BranchCompensate => Branch::BranchAction,
        ][$op] ?? '';

        $this->DBTransaction->beginTransaction();

        try {
            $originAffected = $this->insertBarrier($transType, $gid, $branchId, $originOP, $bid, $op);
            $currentAffected = $this->insertBarrier($transType, $gid, $branchId, $op, $bid, $op);
            // for msg's DoAndSubmit, repeated insert should be rejected.
            if ($op == TransType::MSG && $currentAffected == 0) {
                throw new DuplicatedException();
            }

            if (
                ($op == Operation::BRANCH_CANCEL || $op == Operation::BRANCH_COMPENSATE) && $originAffected > 0 // null compensate
                || $currentAffected == 0// repeated request or dangled request
            ) {
                $this->DBTransaction->commit();
                return true;
            }

            $businessCall();

            $this->DBTransaction->commit();

            return false;
        } catch (\Throwable $throwable) {
            $this->DBTransaction->rollback();
            throw $throwable;
        }
    }

    protected function insertBarrier(string $transType, string $gid, string $branchId, string $op, string $barrierID, string $reason): int
    {
        if (empty($op)) {
            return 0;
        }

        return $this->DBTransaction->execute(
            'INSERT IGNORE INTO `barrier` (trans_type, gid, branch_id, op, barrier_id, reason) values(?,?,?,?,?,?)',
            [$transType, $gid, $branchId, $op, $barrierID, $reason]
        );
    }
}
