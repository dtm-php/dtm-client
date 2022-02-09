<?php

namespace DtmClient\Exception;


use DtmClient\Constants\Result;

class OngingException extends RequestException
{
    public $message = Result::ONGOING;
    public $code = Result::ONGOING_STATUS;
}