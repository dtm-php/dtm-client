<?php

declare(strict_types=1);

namespace DtmClient\DbTransaction;

use Illuminate\Support\Facades\DB;

/**
 * The LaravelDbTransaction use in laravel framework.
 */
class LaravelDbTransaction extends AbstractTransaction
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

    public function execute(string $sql, array $bindings = []): int
    {
        return DB::affectingStatement($sql, $bindings);
    }

    public function query(string $sql, array $bindings = []): bool|array
    {
        return DB::select($sql, $bindings);
    }

    public function connection()
    {
        return DB::connection();
    }
}
