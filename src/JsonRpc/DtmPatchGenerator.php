<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\JsonRpc;

use Hyperf\Rpc\Contract\PathGeneratorInterface;

class DtmPatchGenerator implements PathGeneratorInterface
{
    public function generate(string $service, string $method): string
    {
        return $method;
    }
}
