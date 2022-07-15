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
use DtmClient\Exception\XaTransactionException;

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
     * @throws XaTransactionException
     */
    public function xaHandlePhase2(string $gid, string $branchId, string $op): bool
    {
        $this->DBTransaction->beginTransaction();
        try {
            $xaId = $gid . '-' . $branchId;
            $sql = $this->DBSpecial->getXaSQL($op, $xaId);
            if (! $this->DBTransaction->execute($sql, [])) {
                throw new XaTransactionException(sprintf('xa %s does not exist', $xaId));
            }

            if ($op == Branch::BranchRollback) {
                $sql = $this->DBSpecial->getInsertIgnoreTemplate('barrier (trans_type, gid, branch_id, op, barrier_id, reason) values(?,?,?,?,?,?)', 'uniq_barrier');
                $result = $this->DBTransaction->execute($sql, [TransContext::getTransType(), $gid, $branchId, Operation::ACTION, '01', $op]);
                if (! $result) {
                    throw new XaTransactionException(sprintf($sql . ' error', $xaId));
                }
            }
            $this->DBTransaction->commit();
            return true;
        } catch (\Throwable $throwable) {
            $this->DBTransaction->rollback();
            throw $throwable;
        }
    }

    /**
     * Public handler of LocalTransaction via http/grpc.
     * @param mixed $callback
     * @throws XaTransactionException
     */
    public function xaHandleLocalTrans($callback): void
    {
        $this->DBTransaction->beginTransaction();
        try {
            $xaBranch = TransContext::getGid() . '-' . TransContext::getBranchId();
            $sql = $this->DBSpecial->getXaSQL('start', $xaBranch);
            if (! $this->DBTransaction->execute($sql, [])) {
                throw new XaTransactionException($sql . ' execute error');
            }

            $sql = $this->DBSpecial->getInsertIgnoreTemplate('barrier (trans_type, gid, branch_id, op, barrier_id, reason) values(?,?,?,?,?,?)', 'uniq_barrier');
            if (! $this->DBTransaction->execute($sql, [TransContext::getTransType(), TransContext::getGid(), TransContext::getBranchId(), Operation::ACTION, '01', Operation::ACTION])) {
                throw new XaTransactionException($sql . ' execute error');
            }

            $callback();

            $sql = $this->DBSpecial->getXaSQL('end', $xaBranch);
            if (! $this->DBTransaction->execute($sql, [])) {
                throw new XaTransactionException($sql . ' execute error');
            }

            $sql = $this->DBSpecial->getXaSQL('prepare', $xaBranch);
            if (! $this->DBTransaction->execute($sql, [])) {
                throw new XaTransactionException($sql . ' execute error');
            }
            $this->DBTransaction->commit();
        } catch (\Throwable $throwable) {
            $this->DBTransaction->rollback();
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
