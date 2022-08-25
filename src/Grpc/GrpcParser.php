<?php

namespace DtmClient\Grpc;

use Google\Protobuf\GPBEmpty;
use Google\Protobuf\Internal\Message;
use Hyperf\Grpc\Parser;

class GrpcParser extends Parser
{
    /**
     * @param null|\swoole_http2_response $response
     * @param mixed $deserialize
     * @return \Grpc\StringifyAble[]|Message[]|\swoole_http2_response[]
     */
    public static function parseResponse($response, $deserialize): array
    {
        if (! $response) {
            return ['No response', self::GRPC_ERROR_NO_RESPONSE, $response];
        }
        if (self::isinvalidStatusInDtm($response->statusCode)) {
            $message = $response->headers['grpc-message'] ?? 'Http status Error';
            $code = $response->headers['grpc-status'] ?? ($response->errCode ?: $response->statusCode);
            return [$message, (int) $code, $response];
        }
        $grpcStatus = (int) ($response->headers['grpc-status'] ?? 0);
        if ($grpcStatus !== 0) {
            return [$response->headers['grpc-message'] ?? 'Unknown error', $grpcStatus, $response];
        }
        $data = $response->data ?? '';
        $reply = self::deserializeMessage($deserialize, $data);
        $status = (int) ($response->headers['grpc-status'] ?? 0 ?: 0);
        return [$reply, $status, $response];
    }

    private static function isinvalidStatusInDtm(int $code)
    {
        return $code !== 0 && $code !== 200 && $code !== 400;
    }

    public static function serializeMessage($data)
    {
        if (empty($data)) {
            $data = new GPBEmpty();
        }

        if (method_exists($data, 'encode')) {
            $data = $data->encode();
        } elseif (method_exists($data, 'serializeToString')) {
            $data = $data->serializeToString();
        } elseif (method_exists($data, 'serialize')) {
            /** @noinspection PhpUndefinedMethodInspection */
            $data = $data->serialize();
        }
        return self::pack((string) $data);
    }
}