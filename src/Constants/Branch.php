<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
namespace DtmClient\Constants;

class Branch
{
    // BranchTry branch type for TCC
    public const BranchTry = 'try';

    // BranchConfirm branch type for TCC
    public const BranchConfirm = 'confirm';

    // BranchCancel branch type for TCC
    public const BranchCancel = 'cancel';

    // BranchAction branch type for message, SAGA, XA
    public const BranchAction = 'action';

    // BranchCompensate branch type for SAGA
    public const BranchCompensate = 'compensate';

    // BranchCommit branch type for XA
    public const BranchCommit = 'commit';

    // BranchRollback branch type for XA
    public const BranchRollback = 'rollback';

    // MsgDoBranch0 const for DoAndSubmit barrier branch
    public const MsgDoBranch0 = '00';

    // MsgDoBarrier1 const for DoAndSubmit barrier barrierID
    public const MsgDoBarrier1 = '01';

    // MsgDoOp const for DoAndSubmit barrier op
    public const MsgDoOp = 'msg';
}
