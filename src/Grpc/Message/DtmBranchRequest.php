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
 * Generated from protobuf message <code>dtm.DtmBranchRequest</code>.
 */
class DtmBranchRequest extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string Gid = 1;</code>.
     */
    protected $Gid = '';

    /**
     * Generated from protobuf field <code>string TransType = 2;</code>.
     */
    protected $TransType = '';

    /**
     * Generated from protobuf field <code>string BranchID = 3;</code>.
     */
    protected $BranchID = '';

    /**
     * Generated from protobuf field <code>string Op = 4;</code>.
     */
    protected $Op = '';

    /**
     * Generated from protobuf field <code>bytes BusiPayload = 6;</code>.
     */
    protected $BusiPayload = '';

    /**
     * Generated from protobuf field <code>map<string, string> Data = 5;</code>.
     */
    private $Data;

    /**
     * Constructor.
     *
     * @param array $data {
     *                    Optional. Data for populating the Message object.
     *
     *     @var string $Gid
     *     @var string $TransType
     *     @var string $BranchID
     *     @var string $Op
     *     @var array|\Google\Protobuf\Internal\MapField $Data
     *     @var string $BusiPayload
     * }
     */
    public function __construct($data = null)
    {
        \DtmClient\Grpc\GPBMetadata\Dtm::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string Gid = 1;</code>.
     * @return string
     */
    public function getGid()
    {
        return $this->Gid;
    }

    /**
     * Generated from protobuf field <code>string Gid = 1;</code>.
     * @param string $var
     * @return $this
     */
    public function setGid($var)
    {
        GPBUtil::checkString($var, true);
        $this->Gid = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string TransType = 2;</code>.
     * @return string
     */
    public function getTransType()
    {
        return $this->TransType;
    }

    /**
     * Generated from protobuf field <code>string TransType = 2;</code>.
     * @param string $var
     * @return $this
     */
    public function setTransType($var)
    {
        GPBUtil::checkString($var, true);
        $this->TransType = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string BranchID = 3;</code>.
     * @return string
     */
    public function getBranchID()
    {
        return $this->BranchID;
    }

    /**
     * Generated from protobuf field <code>string BranchID = 3;</code>.
     * @param string $var
     * @return $this
     */
    public function setBranchID($var)
    {
        GPBUtil::checkString($var, true);
        $this->BranchID = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string Op = 4;</code>.
     * @return string
     */
    public function getOp()
    {
        return $this->Op;
    }

    /**
     * Generated from protobuf field <code>string Op = 4;</code>.
     * @param string $var
     * @return $this
     */
    public function setOp($var)
    {
        GPBUtil::checkString($var, true);
        $this->Op = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>map<string, string> Data = 5;</code>.
     * @return \Google\Protobuf\Internal\MapField
     */
    public function getData()
    {
        return $this->Data;
    }

    /**
     * Generated from protobuf field <code>map<string, string> Data = 5;</code>.
     * @param array|\Google\Protobuf\Internal\MapField $var
     * @return $this
     */
    public function setData($var)
    {
        $arr = GPBUtil::checkMapField($var, \Google\Protobuf\Internal\GPBType::STRING, \Google\Protobuf\Internal\GPBType::STRING);
        $this->Data = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>bytes BusiPayload = 6;</code>.
     * @return string
     */
    public function getBusiPayload()
    {
        return $this->BusiPayload;
    }

    /**
     * Generated from protobuf field <code>bytes BusiPayload = 6;</code>.
     * @param string $var
     * @return $this
     */
    public function setBusiPayload($var)
    {
        GPBUtil::checkString($var, false);
        $this->BusiPayload = $var;

        return $this;
    }
}
