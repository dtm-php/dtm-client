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
use DtmClient\Exception\RuntimeException;
use PDO;
use Throwable;

class Dtmimp
{
    protected DBSpecialInterface $DBSpecial;

    public function __construct(DBSpecialInterface $DBSpecial)
    {
        $this->DBSpecial = $DBSpecial;
    }

    /**
     * Handle the callback of commit/rollback.
     */
    public function xaHandlePhase2(PDO $pdo, string $gid, string $branchId, string $op): bool
    {
        try {
            $xaId = $gid . '-' . $branchId;
            if ($op == Branch::BranchRollback) {
                $sql = "INSERT IGNORE INTO barrier (trans_type, gid, branch_id, op, barrier_id, reason) VALUES('%s','%s','%s','%s','%s','%s')";
                $result = $pdo->exec(sprintf($sql, TransContext::getTransType(), $gid, $branchId, Operation::ACTION, '01', $op));
                if ($result === false || ! $pdo->commit()) {
                    throw new RuntimeException(sprintf($sql . ' error', $xaId));
                }
            }

            $sql = $this->DBSpecial->getXaSQL($op, $xaId);
            if ($pdo->exec($sql) === false) {
                throw new RuntimeException(sprintf('xa %s does not exist', $xaId));
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
    public function xaHandleLocalTrans(PDO $pdo, callable $callback): void
    {
        try {
            $xaBranch = TransContext::getGid() . '-' . TransContext::getBranchId();
            $sql = $this->DBSpecial->getXaSQL('start', $xaBranch);
            if ($pdo->exec($sql) === false) {
                throw new RuntimeException($sql . ' execute error');
            }

            $sql = "INSERT IGNORE INTO barrier (trans_type, gid, branch_id, op, barrier_id, reason) VALUES('%s','%s','%s','%s','%s','%s')";
            $sql = sprintf($sql, TransContext::getTransType(), TransContext::getGid(), TransContext::getBranchId(), Operation::ACTION, '01', Operation::ACTION);
            $result = $pdo->exec($sql);
            if ($result === false) {
                throw new RuntimeException($sql . ' execute error');
            }

            $callback();

            $sql = $this->DBSpecial->getXaSQL('end', $xaBranch);
            if ($pdo->exec($sql) === false) {
                throw new RuntimeException($sql . ' execute error');
            }

            $sql = $this->DBSpecial->getXaSQL('prepare', $xaBranch);
            if ($pdo->exec($sql) === false) {
                throw new RuntimeException($sql . ' execute error');
            }
        } catch (\Throwable $throwable) {
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
