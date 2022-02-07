<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Api\ApiInterface;

abstract class AbstractTransaction
{
    protected ApiInterface $api;

    public function generateGid(): string
    {
        return $this->api->generateGid();
    }
}
