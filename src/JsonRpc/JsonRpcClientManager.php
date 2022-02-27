<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\JsonRpc;

class JsonRpcClientManager
{
    /**
     * @var JsonRpcClient[]
     */
    protected array $clients = [];

    public function getClient(string $serviceName): JsonRpcClient
    {
        if (isset($this->clients[$serviceName])) {
            return $this->clients[$serviceName];
        }

        return $this->clients[$serviceName] = make(JsonRpcClient::class)->setServiceName($serviceName)->initClient();
    }
}
