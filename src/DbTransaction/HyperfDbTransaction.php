<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\DbTransaction;

use DtmClient\Config\DatabaseConfigInterface;
use Hyperf\DbConnection\Db;

class HyperfDbTransaction extends AbstractTransaction
{
    public function __construct(DatabaseConfigInterface $config)
    {
        $this->databaseConfig = $config;
    }

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

    public function execute(string $sql, array $bindings = []): int
    {
        return Db::affectingStatement($sql, $bindings);
    }

    public function query(string $sql, array $bindings = []): bool|array
    {
        return Db::select($sql, $bindings);
    }

    public function insert(string $table, array $data = []): bool
    {
        return Db::table($table)->insert($data);
    }

    public function queryBuilder(string $table, mixed $select, array $where = [], int $limit = 1): array
    {
        return Db::table($table)->select($select)->where($where)->limit($limit)->get()->toArray();
    }
}
