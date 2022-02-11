<?php

namespace DtmClient\DbTransaction;

use Hyperf\DbConnection\Db;

class HyperfDbTransaction implements DBTransactionInterface
{
    public function beginTransaction()
    {
        Db::beginTransaction();
    }

    public function commit()
    {
        Db::commit();
    }

    public function rollback()
    {
        Db::rollback();
    }
    
    public function execInsert(string $sql, array $bindings): int
    {
        return Db::affectingStatement($sql, $bindings);
    }
}