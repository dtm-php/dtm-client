<?php

namespace DtmClient\Constants;


use Psr\Http\Message\ResponseInterface;

class Result
{

    // FAILURE for result of a trans/trans branch
    public const FAILURE = 'FAILURE';
    public const FAILURE_STATUS = 409;

    // SUCCESS for result of a trans/trans branch
    public const SUCCESS = 'SUCCESS';
    public const SUCCESS_STATUS = 200;

    // ONGOING for result of a trans/trans branch
    public const ONGOING = 'ONGOING';
    public const ONGOING_STATUS = 425;

    public static function isOngoing(ResponseInterface $response)
    {
        return $response->getStatusCode() === self::ONGOING_STATUS;
    }

    public static function isSuccess(ResponseInterface $response)
    {
        return $response->getStatusCode() === self::SUCCESS_STATUS;
    }

    public static function isFailure(ResponseInterface $response)
    {
        return $response->getStatusCode() === self::FAILURE_STATUS;
    }

}