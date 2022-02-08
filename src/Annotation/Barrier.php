<?php

namespace DtmClient\Annotation;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
#[Attribute(Attribute::TARGET_METHOD)]
class Barrier
{
    public string $dbType = 'mysql';

    public function __construct(string $dbType)
    {
        
    }
}