<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\DBSpecial;

use DtmClient\Constants\DbType;
use DtmClient\Exception\UnsupportedException;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class DBSpecialFactory
{
    /**
     * @throws UnsupportedException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): DBSpecialInterface
    {
        $type = $container->get(ConfigInterface::class)->get('dtm.barrier.db.type', DbType::MySQL);
        return match ($type) {
            DbType::MySQL => $container->get(MySqlDBSpecial::class),
            default => throw new UnsupportedException('Barrier DB type is unsupported.'),
        };
    }
}
