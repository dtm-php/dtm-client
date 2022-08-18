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
}
