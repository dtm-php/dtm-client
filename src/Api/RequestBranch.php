<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
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

    public string $jsonRpcServiceName = '';

    public array $jsonRpcServiceParams = [];

    public string $phase2Url = '';
}
