<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\DbTransaction;

use Illuminate\Support\Facades\DB;

/**
 * The LaravelDbTransaction use in laravel framework.
 */
class LaravelDbTransaction implements DBTransactionInterface
{
    public function beginTransaction()
    {
        DB::beginTransaction();
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

    public function execute(string $sql, array $bindings): bool
    {
        return Db::statement($sql, $bindings);
    }
}
