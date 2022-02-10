<?php

namespace DtmClient\Grpc;


use DtmClient\Constants\Operation;
use DtmClient\Grpc\Message\DtmBranchRequest;
use DtmClient\Grpc\Message\DtmGidReply;
use DtmClient\Grpc\Message\DtmRequest;
use DtmClient\TransContext;
use Google\Protobuf\GPBEmpty;
use Google\Protobuf\Internal\Message;
use Hyperf\GrpcClient\BaseClient;

class GrpcClient extends BaseClient
{

    protected const SERVICE = '/dtmgimp.Dtm/';

    public function newGid(): DtmGidReply
    {
        [$reply] = $this->_simpleRequest(
            self::SERVICE . 'NewGid',
            new GPBEmpty(),
            [DtmGidReply::class, 'decode']
        );
        return $reply;
    }

    public function transCallDtm(Message $argument, string $operation, string $replyClass = '')
    {
        [$reply] = $this->_simpleRequest(
            self::SERVICE . ucfirst($operation),
            $argument,
            [$replyClass ?: GPBEmpty::class, 'decode']
        );
        return $reply;
    }

    public function invoke(string $method, Message $argument, array $deserialize, array $metadata = [], array $options = []): array
    {
        $response = $this->_simpleRequest($method, $argument, $deserialize, $metadata, $options);
        return $response;
    }

}