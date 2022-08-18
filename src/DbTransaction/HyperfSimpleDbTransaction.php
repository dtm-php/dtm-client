<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\DbTransaction;

use Hyperf\Contract\ConfigInterface;
use Hyperf\DB\DB;

class HyperfSimpleDbTransaction extends AbstractTransaction
{
    public function __construct(ConfigInterface $config)
    {
        $this->databaseConfig = $config->get('dtm.database');
    }

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
        return DB::execute($sql, $bindings);
    }
}
