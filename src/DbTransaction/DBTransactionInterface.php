<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\DbTransaction;

interface DBTransactionInterface
{
    public function beginTransaction();

    public function commit();

    public function rollback();

    public function execInsert(string $sql, array $bindings): int;

    public function execute(string $sql, array $bindings);
}
