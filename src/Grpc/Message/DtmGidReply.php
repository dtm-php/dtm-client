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
 * Generated from protobuf message <code>dtm.DtmGidReply</code>.
 */
class DtmGidReply extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string Gid = 1;</code>.
     */
    protected $Gid = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *                    Optional. Data for populating the Message object.
     *
     *     @var string $Gid
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
}
