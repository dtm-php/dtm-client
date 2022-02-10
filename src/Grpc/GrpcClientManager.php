<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Grpc;

use DtmClient\TransContext;
use Hyperf\GrpcClient\BaseClient;

class GrpcClientManager
{
    protected array $clients = [];

    public function getClient(string $hostname): BaseClient
    {
        if (! isset($this->clients[$hostname])) {
            $this->addClientWithHostname($hostname);
        }

        $client = $this->clients[$hostname];
        $client && TransContext::setDtm($hostname);
        return $client;
    }

    public function addClient(string $hostname, BaseClient $client)
    {
        $this->clients[$hostname] = $client;
    }

    public function addClientWithHostname(string $hostname)
    {
        $this->clients[$hostname] = new UniversalGrpcClient($hostname);
    }
}
