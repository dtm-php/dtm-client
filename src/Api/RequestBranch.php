<?php

namespace DtmClient\Api;


use Google\Protobuf\GPBEmpty;
use Google\Protobuf\Internal\Message;

class RequestBranch
{

    public string $method;
    public array $body = [];
    public string $branchId;
    public string $op;
    public string $url;
    public array $branchHeaders = [];
    public Message $grpcArgument;
    public array $grpcMetadata = [];
    public array $grpcDeserialize = [GPBEmpty::class, 'decode'];
    public array $grpcOptions = [];

}