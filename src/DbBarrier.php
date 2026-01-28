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
use DtmClient\Exception\FailureException;

class DbBarrier extends MySqlBarrier
{
    public function queryPrepared(string $transType, string $gid): bool
    {
        $db = $this->DBTransaction->connection();
        $table = $db->raw('barrier');
        $db->table($table)->insertOrIgnore([
            'trans_type' => $transType,
            'gid' => $gid,
            'branch_id' => Branch::MsgDoBranch0,
            'op' => Branch::MsgDoOp,
            'barrier_id' => Branch::MsgDoBarrier1,
            'reason' => Operation::ROLLBACK,
        ]);

        $reason = $db->table($table)
            ->select('reason')
            ->where([
                'gid' => $gid,
                'branch_id' => Branch::MsgDoBranch0,
                'op' => Branch::MsgDoOp,
                'barrier_id' => Branch::MsgDoBarrier1,
            ])->first();

        if ($reason->reason == Operation::ROLLBACK) {
            throw new FailureException();
        }
        return true;
    }

    protected function insertBarrier(string $transType, string $gid, string $branchId, string $op, string $barrierID, string $reason): int
    {
        if (empty($op)) {
            return 0;
        }
        $db = $this->DBTransaction->connection();
        $table = $db->raw('barrier');
        return $db->table($table)->insertOrIgnore([
            'trans_type' => $transType,
            'gid' => $gid,
            'branch_id' => $branchId,
            'op' => $op,
            'barrier_id' => $barrierID,
            'reason' => $reason,
        ]);
    }
}
