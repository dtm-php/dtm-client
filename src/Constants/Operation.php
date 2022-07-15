<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Constants;

class Operation
{
    public const PREPARE = 'prepare';

    public const SUBMIT = 'submit';

    public const ABORT = 'abort';

    public const REGISTER_BRANCH = 'registerBranch';

    public const QUERY = 'query';

    public const QUERY_ALL = 'all';

    public const TRY = 'try';

    public const BRANCH_CANCEL = 'cancel';

    public const BRANCH_COMPENSATE = 'compensate';

    public const ACTION = 'action';
}
