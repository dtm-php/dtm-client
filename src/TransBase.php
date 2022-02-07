<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient;

class TransBase
{
    use TransOption;

    public string $gid;

    public string $transType;

    public string $dtm;

    public string $customData;

    /**
     * Use in MSG/SAGA.
     */
    public array $steps;

    /**
     * Use in MSG/SAGA.
     * @var string[]
     */
    public array $payloads = [];

    public array $binPayLoads;

    /**
     * Use in XA/TCC.
     */
    public string $branchId;

    /**
     * Use in XA/TCC.
     */
    public int $subBranchId;

    /**
     * Use in XA/TCC.
     */
    public string $op;

    /**
     * Use in MSG.
     */
    public string $queryPrepared;
}
