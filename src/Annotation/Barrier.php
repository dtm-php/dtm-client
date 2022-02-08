<?php

namespace DtmClient\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Barrier
{
    public string $dbType = 'mysql';

    public function __construct(string $dbType = 'mysql')
    {
        
    }
}