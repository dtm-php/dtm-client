<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
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

    public function execute(string $sql, array $bindings = [])
    {
        return Db::affectingStatement($sql, $bindings);
    }

    public function query(string $sql, array $bindings = [])
    {
        return Db::select($sql, $bindings);
    }
}
