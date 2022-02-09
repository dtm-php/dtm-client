<?php

namespace DtmClient\Exception;


use DtmClient\Constants\Result;

class FailureException extends RequestException
{

    public $message = Result::FAILURE;
    public $code = Result::FAILURE_STATUS;

}