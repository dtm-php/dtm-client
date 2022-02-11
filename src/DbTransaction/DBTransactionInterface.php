<?php
namespace DtmClient\DbTransaction;

interface DBTransactionInterface
{
    public function beginTransaction();

    public function commit();

    public function rollback();
    
    public function execInsert(string $sql, array $bindings): int;
}