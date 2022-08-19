<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Grpc;

use Google\Protobuf\Internal\Message;
use Hyperf\Grpc\Parser;
use Hyperf\Grpc\StatusCode;
use Hyperf\GrpcClient\BaseClient;
use Hyperf\GrpcClient\Exception\GrpcClientException;
use Swoole\Http2\Response;

class UniversalGrpcClient extends BaseClient
{
    /**
     * Call a remote method that takes a single argument and has a
     * single output.
     *
     * @param string $method The name of the method to call
     * @param Message $argument The argument to the method
     * @param callable $deserialize A function that deserializes the response
     * @throws GrpcClientException
     * @return array|\Google\Protobuf\Internal\Message[]|Response[]
     */
    protected function _simpleRequest(
        string $method,
        Message $argument,
        $deserialize,
        array $metadata = [],
        array $options = []
    ) {
        $options['headers'] = ($options['headers'] ?? []) + $metadata;
        $streamId = retry($this->options['retry_attempts'] ?? 3, function () use ($method, $argument, $options) {
            $streamId = $this->send($this->buildRequest($method, $argument, $options));
            if ($streamId <= 0) {
                $this->init();
                // The client should not be used after this exception
                throw new GrpcClientException('Failed to send the request to server', StatusCode::INTERNAL);
            }
            return $streamId;
        }, $this->options['retry_interval'] ?? 100);
        return GrpcParser::parseResponse($this->recv($streamId), $deserialize);
    }

    public function invoke(string $method, Message $argument, array $deserialize, array $metadata = [], array $options = []): array
    {
        var_dump('invoke');
        return $this->_simpleRequest($method, $argument, $deserialize, $metadata, $options);
    }
}
