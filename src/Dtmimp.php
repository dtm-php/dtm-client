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
use DtmClient\DBSpecial\DBSpecialInterface;
use DtmClient\DbTransaction\DBTransactionInterface;
use DtmClient\Exception\RuntimeException;

class Dtmimp
{
    protected DBTransactionInterface $DBTransaction;

    protected DBSpecialInterface $DBSpecial;

    public function __construct(DBTransactionInterface $DBTransaction, DBSpecialInterface $DBSpecial)
    {
        $this->DBTransaction = $DBTransaction;
        $this->DBSpecial = $DBSpecial;
    }

    /**
     * Handle the callback of commit/rollback.
     */
    public function xaHandlePhase2(string $gid, string $branchId, string $op): bool
    {
        $xaId = $gid . '-' . $branchId;
        $sql = $this->DBSpecial->getXaSQL($op, $xaId);
        if (! $this->DBTransaction->execute($sql, [])) {
            // Repeat commit/rollback with the same id, report this error, ignore
            throw new RuntimeException(sprintf('xa %s does not exist', $xaId));
        }

        if ($op == Branch::BranchRollback) {
            // rollback insert a row after prepare. no-error means prepare has finished.
            $sql = 'INSERT IGNOR INTO barrier (trans_type, gid, branch_id, op, barrier_id, reason) VALUES(?,?,?,?,?,?)';
            $result = $this->DBTransaction->execInsert($sql, [TransContext::getTransType(), $gid, $branchId, Operation::ACTION, '01', $op]);
            if (! $result) {
                throw new RuntimeException(sprintf('xaId:%s rollback insert a row after prepare fail. ', $xaId));
            }
        }
        return true;
    }

    /**
     * Public handler of LocalTransaction via http/grpc.
     * @throws RuntimeException
     */
    public function xaHandleLocalTrans(callable $callback): void
    {
        $xaBranch = TransContext::getGid() . '-' . TransContext::getBranchId();
        $sql = $this->DBSpecial->getXaSQL('start', $xaBranch);
        if (! $this->DBTransaction->execute($sql, [])) {
            throw new RuntimeException($sql . ' execute error');
        }

        $sql  = 'INSERT IGNOR INTO barrier (trans_type, gid, branch_id, op, barrier_id, reason) VALUES(?,?,?,?,?,?)';
        if (! $this->DBTransaction->execInsert($sql, [TransContext::getTransType(), TransContext::getGid(), TransContext::getBranchId(), Operation::ACTION, '01', Operation::ACTION])) {
            throw new RuntimeException($sql . ' execute error');
        }

        $callback();

        $sql = $this->DBSpecial->getXaSQL('end', $xaBranch);
        if (! $this->DBTransaction->execute($sql, [])) {
            throw new RuntimeException($sql . ' execute error');
        }

        $sql = $this->DBSpecial->getXaSQL('prepare', $xaBranch);
        if (! $this->DBTransaction->execute($sql, [])) {
            throw new RuntimeException($sql . ' execute error');
        }
    }

    public function initTransBase(string $gid, string $transType, string $branchId): void
    {
        TransContext::setGid($gid);
        TransContext::setBranchId($branchId);
        TransContext::setTransType($transType);
    }
}
