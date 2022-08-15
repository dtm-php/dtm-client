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
use Throwable;

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
        try {
            $xaId = $gid . '-' . $branchId;
            $sql = $this->DBSpecial->getXaSQL($op, $xaId);
            try {
                $this->dbTransaction->xaExec($sql);
            } catch (PDOException $exception) {
                // Repeat commit/rollback with the same id, report this error, ignore
                if (! str_contains($exception->getMessage(), 'XAER_NOTA') && ! str_contains($exception->getMessage(), 'does not exist')) {
                    throw new RuntimeException($sql . ' execute error');
                }
            }

            if ($op == Branch::BranchRollback) {
                // rollback insert a row after prepare. no-error means prepare has finished.
                $sql = 'INSERT IGNORE INTO barrier (trans_type, gid, branch_id, op, barrier_id, reason) VALUES(?,?,?,?,?,?)';
                $result = $this->dbTransaction->xaExecute($sql, [TransContext::getTransType(), $gid, $branchId, Operation::ACTION, '01', $op]);
                if (! $result) {
                    // Repeat commit/rollback with the same id, report this error, ignore
                    throw new RuntimeException(sprintf($sql . ' error', $xaId));
                }
            }

            return true;
        } catch (Throwable $throwable) {
            throw $throwable;
        }
    }

    /**
     * Public handler of LocalTransaction via http/grpc.
     * @throws RuntimeException
     */
    public function xaHandleLocalTrans(callable $callback): void
    {
        $xaId = TransContext::getGid() . '-' . TransContext::getBranchId();
        $sql = $this->DBSpecial->getXaSQL('start', $xaId);
        $res = $this->dbTransaction->xaExec($sql);
        var_dump('start xa', $res);

        // prepare and rollback both insert a row
        $sql = 'INSERT IGNORE INTO barrier (trans_type, gid, branch_id, op, barrier_id, reason) VALUES(?,?,?,?,?,?)';
        $result = $this->dbTransaction->xaExecute($sql, [TransContext::getTransType(), TransContext::getGid(), TransContext::getBranchId(), Operation::ACTION, '01', Operation::ACTION]);
        var_dump($result);
        if (! $result) {
            throw new RuntimeException($sql . ' execute error');
        }

        $callback();

        $sql = $this->DBSpecial->getXaSQL('end', $xaId);
        var_dump('end xa');
        $this->dbTransaction->xaExec($sql);
        var_dump('xx end xa');
        $sql = $this->DBSpecial->getXaSQL('prepare', $xaId);
        $this->dbTransaction->xaExec($sql);
    }

    public function initTransBase(string $gid, string $transType, string $branchId): void
    {
        TransContext::setGid($gid);
        TransContext::setBranchId($branchId);
        TransContext::setTransType($transType);
    }
}
