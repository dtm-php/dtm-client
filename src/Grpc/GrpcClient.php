<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Grpc;

use DtmClient\Grpc\Message\DtmGidReply;
use Google\Protobuf\GPBEmpty;
use Google\Protobuf\Internal\Message;

class GrpcClient extends UniversalGrpcClient
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
}
