<?php

namespace DtmClient\Grpc;


use Google\Protobuf\Internal\Message;
use Hyperf\GrpcClient\BaseClient;

class UniversalGrpcClient extends BaseClient
{

    public function invoke(string $method, Message $argument, array $deserialize, array $metadata = [], array $options = []): array
    {
        $response = $this->_simpleRequest($method, $argument, $deserialize, $metadata, $options);
        return $response;
    }

}