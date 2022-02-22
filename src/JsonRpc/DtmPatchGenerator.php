<?php

namespace DtmClient\JsonRpc;

use Hyperf\Rpc\Contract\PathGeneratorInterface;

class DtmPatchGenerator implements PathGeneratorInterface
{
    public function generate(string $service, string $method): string
    {
        var_dump($service, $method);
        var_dump('DtmPatchGenerator::generate');
        var_dump('---------------');
        return sprintf('%s.%s', $service, $method);
    }
}