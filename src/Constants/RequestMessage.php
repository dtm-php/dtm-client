<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Constants;

class RequestMessage
{
    // ResultFailure for result of a trans/trans branch
    public const ResultFailure = 'FAILURE';

    // ResultSuccess for result of a trans/trans branch
    public const ResultSuccess = 'SUCCESS';

    // ResultOngoing for result of a trans/trans branch
    public const ResultOngoing = 'ONGOING';

    // DBTypeMysql const for driver mysql
    public const DBTypeMysql = 'mysql';

    // DBTypePostgres const for driver postgres
    public const DBTypePostgres = 'postgres';

    // DBTypeRedis const for driver redis
    public const DBTypeRedis = 'redis';
}
