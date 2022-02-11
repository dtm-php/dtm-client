<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Exception;

use DtmClient\Constants\Result;

class DuplicatedException extends RequestException
{
    public $message = Result::ERR_DUPLICATED;

    public $code = Result::ERR_DUPLICATED_STATUS;
}
