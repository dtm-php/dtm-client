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

    public function execInsert(string $sql, array $bindings, string $pool = 'default', bool $isXa = false): int
    {
        return self::connection($pool, $isXa)->affectingStatement($sql, $bindings);
    }

    public function execute(string $sql, array $bindings, string $pool = 'default', bool $isXa = false): bool
    {
        return self::connection($pool, $isXa)->statement($sql, $bindings);
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
