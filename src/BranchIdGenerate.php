<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

use DtmClient\Exception\GenerateException;

class BranchIdGenerate implements BranchIdGenerateInterface
{
    private ?string $branchId = null;

    private int $subBranchId = 0;

    public function generateSubBranchId(): string
    {
        $this->branchId === null &&  $this->branchId = TransContext::getBranchId();
        if ($this->subBranchId >= 99) {
            throw new GenerateException('branch id is larger than 99');
        }


        if (strlen($this->branchId) >= 20) {
            throw new GenerateException('total branch id is longer than 20');
        }

        $this->subBranchId = $this->subBranchId + 1;
        return $this->getCurrentSubBranchID();
    }

    public function getCurrentSubBranchID(): string
    {
        return $this->branchId . sprintf('%02d', $this->subBranchId);
    }
}
