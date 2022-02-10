<?php

namespace DtmClient;

interface BarrierInterface
{
    public function call(): bool;
}