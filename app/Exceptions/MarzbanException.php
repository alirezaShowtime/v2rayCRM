<?php

namespace App\Exceptions;

use Throwable;

class MarzbanException extends \Exception
{
    public const CONFIG_NOT_FOUND = 1;
    public const LOGIN_FAILED = 2;
    public const CONFIG_ALREADY_ADDED = 3;
    public const CREATE_CONFIG_FAILED = 4;
    public const SAVE_TOKEN_FAILED = 5;
    public const GET_CONFIGS_FAILED = 6;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
