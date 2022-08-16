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
use PDOException;

class DtmImp
{
    protected DBSpecialInterface $DBSpecial;

    protected DBTransactionInterface $dbTransaction;

    public function __construct(DBSpecialInterface $DBSpecial, DBTransactionInterface $dbTransaction)
    {
        $this->DBSpecial = $DBSpecial;
        $this->dbTransaction = $dbTransaction;
    }

    /**
     * Handle the callback of commit/rollback.
     */
    public function xaHandlePhase2(string $gid, string $branchId, string $op): bool
    {
        $xaId = $gid . '-' . $branchId;
        $sql = $this->DBSpecial->getXaSQL($op, $xaId);
        try {
            $this->dbTransaction->xaExec($sql);
        } catch (PDOException $exception) {
            // Repeat commit/rollback with the same id, report this error, ignore
            if (! str_contains($exception->getMessage(), 'XAER_NOTA') && ! str_contains($exception->getMessage(), 'does not exist')) {
                throw $exception;
            }
        }

        if ($op == Branch::BranchRollback) {
            // rollback insert a row after prepare. no-error means prepare has finished.
            $this->dbTransaction->xaExecute(
                'INSERT IGNORE INTO barrier (trans_type, gid, branch_id, op, barrier_id, reason) VALUES(?,?,?,?,?,?)',
                [TransContext::getTransType(), $gid, $branchId, Operation::ACTION, '01', $op]
            );
        }

        return true;
    }

    /**
     * Public handler of LocalTransaction via http/grpc.
     * @throws RuntimeException
     * @throws PDOException
     */
    public function xaHandleLocalTrans(callable $callback): void
    {
        $xaId = TransContext::getGid() . '-' . TransContext::getBranchId();
        $sql = $this->DBSpecial->getXaSQL('start', $xaId);
        $this->dbTransaction->xaExec($sql);
        try {
            // prepare and rollback both insert a row
            $sql = 'INSERT IGNORE INTO barrier (trans_type, gid, branch_id, op, barrier_id, reason) VALUES(?,?,?,?,?,?)';
            $result = $this->dbTransaction->xaExecute($sql, [TransContext::getTransType(), TransContext::getGid(), TransContext::getBranchId(), Operation::ACTION, '01', Operation::ACTION]);
            if (! $result) {
                throw new RuntimeException($sql . ' execute error');
            }

            $callback();

            $sql = $this->DBSpecial->getXaSQL('end', $xaId);
            $this->dbTransaction->xaExec($sql);
            $sql = $this->DBSpecial->getXaSQL('prepare', $xaId);
            $this->dbTransaction->xaExec($sql);
        } catch (\Throwable $throwable) {
            $sql = $this->DBSpecial->getXaSQL('rollback', $xaId);
            $this->dbTransaction->xaExec($sql);
            throw $throwable;
        }
    }

    public function initTransBase(string $gid, string $transType, string $branchId): void
    {
        TransContext::setGid($gid);
        TransContext::setBranchId($branchId);
        TransContext::setTransType($transType);
    }
}
