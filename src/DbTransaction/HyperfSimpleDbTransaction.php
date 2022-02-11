<?php

namespace DtmClient\DbTransaction;

use Hyperf\DB\DB;

class HyperfSimpleDbTransaction implements DBTransactionInterface
{
    public function beginTransaction()
    {
        DB::beginTransaction();
    }

    public function commit()
    {
        DB::commit();
    }

    public function rollback()
    {
        DB::rollback();
    }
    
    public function execInsert(string $sql, array $bindings): int
    {
        return DB::execute($sql, $bindings);
    }
}