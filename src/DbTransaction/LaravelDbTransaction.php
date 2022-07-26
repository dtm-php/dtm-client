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

    public function execInsert(string $sql, array $bindings, string $pool = 'default', bool $isXa = false): int
    {
        $db = Db::connection($pool);
        if ($isXa) {
            /** @var \PDO $pdo */
            $pdo = $db->getPdo();
            $pdo->setAttribute(0, 'autocommit');
            $db->setPdo($pdo);
        }
        return $db->affectingStatement($sql, $bindings);
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
