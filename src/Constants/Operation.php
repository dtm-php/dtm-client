<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
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
}
