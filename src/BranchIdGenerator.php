<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Exception\GenerateException;

class BranchIdGenerator implements BranchIdGeneratorInterface
{
    public function generateSubBranchId(): string
    {
        $branchId = TransContext::getBranchId();
        $subBranchId = TransContext::getSubBranchId() ?? 0;
        if ($subBranchId >= 99) {
            throw new GenerateException('Branch ID can not larger than 99');
        }

        if (strlen($branchId) >= 20) {
            throw new GenerateException('Total Branch ID can not longer than 20');
        }

        $subBranchId = $subBranchId + 1;
        return $this->getCurrentSubBranchId($subBranchId);
    }

    public function getCurrentSubBranchId(int $subBranchId): string
    {
        return TransContext::getBranchId() . sprintf('%02d', $subBranchId);
    }
}
