<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\DBSpecial;

interface DBSpecialInterface
{
    public function getPlaceHoldSQL(string $sql): string;

    public function getXaSQL(string $command, string $xid): string;
}
