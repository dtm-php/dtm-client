<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

interface BranchIdGeneratorInterface
{
    public function generateSubBranchId(): string;

    public function getCurrentSubBranchId(int $subBranchId): string;
}
