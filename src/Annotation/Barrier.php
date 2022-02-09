<?php

namespace DtmClient\Annotation;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_METHOD)]
class Barrier extends AbstractAnnotation
{
    public string $dbType = 'mysql';

    public function __construct(string $dbType = 'mysql')
    {
        
    }
}