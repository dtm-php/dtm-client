<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Constants;

class Status
{
    // StatusPrepared status for global/branch trans status.
    public const StatusPrepared = 'prepared';

    // StatusSubmitted status for global trans status.
    public const StatusSubmitted = 'submitted';

    // StatusSucceed status for global/branch trans status.
    public const StatusSucceed = 'succeed';

    // StatusFailed status for global/branch trans status.
    public const StatusFailed = 'failed';

    // StatusAborting status for global trans status.
    public const StatusAborting = 'aborting';
}
