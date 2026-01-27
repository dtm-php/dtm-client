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

    public function insert(string $table, array $data = []): bool
    {
        return DB::table($table)->insert($data);
    }

    public function queryBuilder(string $table, mixed $select, array $where = [], int $limit = 1): array
    {
        return DB::table($table)->select($select)->where($where)->limit($limit)->get()->toArray();
    }
}
