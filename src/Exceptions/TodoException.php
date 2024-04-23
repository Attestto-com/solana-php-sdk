<?php

namespace Attestto\SolanaPhpSdk\Exceptions;

use Exception;
use Throwable;

class TodoException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message . " | Help is appreciated: https://github.com/attestto-com/solana-php-sdk", $code, $previous);
    }
}
