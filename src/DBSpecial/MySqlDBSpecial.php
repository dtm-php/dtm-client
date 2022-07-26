<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\DBSpecial;

class MySqlDBSpecial implements DBSpecialInterface
{
    public function getPlaceHoldSQL(string $sql): string
    {
        return $sql;
    }

    public function getXaSQL(string $command, string $xid): string
    {
        return sprintf("XA %s '%s'", $command, $xid);
    }
}
