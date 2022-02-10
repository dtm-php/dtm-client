<?php

declare(strict_types=1);
/**
 * This file is part of DTM-PHP.
 *
 * @license  https://github.com/dtm-php/dtm-client/blob/master/LICENSE
 */
# source: dtm.proto

namespace DtmClient\Grpc\Message;

use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>dtm.DtmTransOptions</code>.
 */
class DtmTransOptions extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>bool WaitResult = 1;</code>.
     */
    protected $WaitResult = false;

    /**
     * Generated from protobuf field <code>int64 TimeoutToFail = 2;</code>.
     */
    protected $TimeoutToFail = 0;

    /**
     * Generated from protobuf field <code>int64 RetryInterval = 3;</code>.
     */
    protected $RetryInterval = 0;

    /**
     * Generated from protobuf field <code>repeated string PassthroughHeaders = 4;</code>.
     */
    private $PassthroughHeaders;

    /**
     * Generated from protobuf field <code>map<string, string> BranchHeaders = 5;</code>.
     */
    private $BranchHeaders;

    /**
     * Constructor.
     *
     * @param array $data {
     *                    Optional. Data for populating the Message object.
     *
     *     @var bool $WaitResult
     *     @var int|string $TimeoutToFail
     *     @var int|string $RetryInterval
     *     @var \Google\Protobuf\Internal\RepeatedField|string[] $PassthroughHeaders
     *     @var array|\Google\Protobuf\Internal\MapField $BranchHeaders
     * }
     */
    public function __construct($data = null)
    {
        \DtmClient\Grpc\GPBMetadata\Dtm::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>bool WaitResult = 1;</code>.
     * @return bool
     */
    public function getWaitResult()
    {
        return $this->WaitResult;
    }

    /**
     * Generated from protobuf field <code>bool WaitResult = 1;</code>.
     * @param bool $var
     * @return $this
     */
    public function setWaitResult($var)
    {
        GPBUtil::checkBool($var);
        $this->WaitResult = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 TimeoutToFail = 2;</code>.
     * @return int|string
     */
    public function getTimeoutToFail()
    {
        return $this->TimeoutToFail;
    }

    /**
     * Generated from protobuf field <code>int64 TimeoutToFail = 2;</code>.
     * @param int|string $var
     * @return $this
     */
    public function setTimeoutToFail($var)
    {
        GPBUtil::checkInt64($var);
        $this->TimeoutToFail = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 RetryInterval = 3;</code>.
     * @return int|string
     */
    public function getRetryInterval()
    {
        return $this->RetryInterval;
    }

    /**
     * Generated from protobuf field <code>int64 RetryInterval = 3;</code>.
     * @param int|string $var
     * @return $this
     */
    public function setRetryInterval($var)
    {
        GPBUtil::checkInt64($var);
        $this->RetryInterval = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated string PassthroughHeaders = 4;</code>.
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getPassthroughHeaders()
    {
        return $this->PassthroughHeaders;
    }

    /**
     * Generated from protobuf field <code>repeated string PassthroughHeaders = 4;</code>.
     * @param \Google\Protobuf\Internal\RepeatedField|string[] $var
     * @return $this
     */
    public function setPassthroughHeaders($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->PassthroughHeaders = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>map<string, string> BranchHeaders = 5;</code>.
     * @return \Google\Protobuf\Internal\MapField
     */
    public function getBranchHeaders()
    {
        return $this->BranchHeaders;
    }

    /**
     * Generated from protobuf field <code>map<string, string> BranchHeaders = 5;</code>.
     * @param array|\Google\Protobuf\Internal\MapField $var
     * @return $this
     */
    public function setBranchHeaders($var)
    {
        $arr = GPBUtil::checkMapField($var, \Google\Protobuf\Internal\GPBType::STRING, \Google\Protobuf\Internal\GPBType::STRING);
        $this->BranchHeaders = $arr;

        return $this;
    }
}
