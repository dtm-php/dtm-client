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
 * DtmRequest request sent to dtm server.
 *
 * Generated from protobuf message <code>dtm.DtmRequest</code>
 */
class DtmRequest extends \Google\Protobuf\Internal\Message
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
     * Generated from protobuf field <code>.dtm.DtmTransOptions TransOptions = 3;</code>.
     */
    protected $TransOptions;

    /**
     * Generated from protobuf field <code>string CustomedData = 4;</code>.
     */
    protected $CustomedData = '';

    /**
     * for MSG.
     *
     * Generated from protobuf field <code>string QueryPrepared = 6;</code>
     */
    protected $QueryPrepared = '';

    /**
     * Generated from protobuf field <code>string Steps = 7;</code>.
     */
    protected $Steps = '';

    /**
     * for MSG/SAGA branch payloads.
     *
     * Generated from protobuf field <code>repeated bytes BinPayloads = 5;</code>
     */
    private $BinPayloads;

    /**
     * Constructor.
     *
     * @param array $data {
     *                    Optional. Data for populating the Message object.
     *
     *     @var string $Gid
     *     @var string $TransType
     *     @var \DtmClient\Grpc\Message\DtmTransOptions $TransOptions
     *     @var string $CustomedData
     *     @var \Google\Protobuf\Internal\RepeatedField|string[] $BinPayloads
     *           for MSG/SAGA branch payloads
     *     @var string $QueryPrepared
     *           for MSG
     *     @var string $Steps
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
     * Generated from protobuf field <code>.dtm.DtmTransOptions TransOptions = 3;</code>.
     * @return null|\DtmClient\Grpc\Message\DtmTransOptions
     */
    public function getTransOptions()
    {
        return $this->TransOptions;
    }

    public function hasTransOptions()
    {
        return isset($this->TransOptions);
    }

    public function clearTransOptions()
    {
        unset($this->TransOptions);
    }

    /**
     * Generated from protobuf field <code>.dtm.DtmTransOptions TransOptions = 3;</code>.
     * @param \DtmClient\Grpc\Message\DtmTransOptions $var
     * @return $this
     */
    public function setTransOptions($var)
    {
        GPBUtil::checkMessage($var, \DtmClient\Grpc\Message\DtmTransOptions::class);
        $this->TransOptions = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string CustomedData = 4;</code>.
     * @return string
     */
    public function getCustomedData()
    {
        return $this->CustomedData;
    }

    /**
     * Generated from protobuf field <code>string CustomedData = 4;</code>.
     * @param string $var
     * @return $this
     */
    public function setCustomedData($var)
    {
        GPBUtil::checkString($var, true);
        $this->CustomedData = $var;

        return $this;
    }

    /**
     * for MSG/SAGA branch payloads.
     *
     * Generated from protobuf field <code>repeated bytes BinPayloads = 5;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getBinPayloads()
    {
        return $this->BinPayloads;
    }

    /**
     * for MSG/SAGA branch payloads.
     *
     * Generated from protobuf field <code>repeated bytes BinPayloads = 5;</code>
     * @param \Google\Protobuf\Internal\RepeatedField|string[] $var
     * @return $this
     */
    public function setBinPayloads($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::BYTES);
        $this->BinPayloads = $arr;

        return $this;
    }

    /**
     * for MSG.
     *
     * Generated from protobuf field <code>string QueryPrepared = 6;</code>
     * @return string
     */
    public function getQueryPrepared()
    {
        return $this->QueryPrepared;
    }

    /**
     * for MSG.
     *
     * Generated from protobuf field <code>string QueryPrepared = 6;</code>
     * @param string $var
     * @return $this
     */
    public function setQueryPrepared($var)
    {
        GPBUtil::checkString($var, true);
        $this->QueryPrepared = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string Steps = 7;</code>.
     * @return string
     */
    public function getSteps()
    {
        return $this->Steps;
    }

    /**
     * Generated from protobuf field <code>string Steps = 7;</code>.
     * @param string $var
     * @return $this
     */
    public function setSteps($var)
    {
        GPBUtil::checkString($var, true);
        $this->Steps = $var;

        return $this;
    }
}
