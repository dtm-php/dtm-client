<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
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

    public function execInsert(string $sql, array $bindings, string $pool = 'default', bool $isXa = false): int
    {
        return $this->execute($sql, $bindings, $pool, $isXa);
    }

    public function execute(string $sql, array $bindings, string $pool = 'default', bool $isXa = false)
    {
        return self::connection($pool, $isXa)->execute($sql, $bindings);
    }

    public static function connection(string $pool = 'default', bool $isXa = false)
    {
        $db = Db::connection($pool);
        if ($isXa) {
            /** @var \PDO $pdo */
            $pdo = $db->getPdo();
            $pdo->setAttribute(0, 'autocommit');
            $db->setPdo($pdo);
        }
        return $db;
    }
}
