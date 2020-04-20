<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Exceptions;

use Exception;
use Spool\Pedis\Constants\ApiErrorCode;
/**
 * Description of PedisException
 *
 * @author 陈浩波
 */
class PedisException extends Exception
{
    public function render(Exception $exception): string
    {
        if ($exception instanceof PedisException) {
            $code = $exception->getCode();
            if (!$code || $code < 0) {
                $code = is_numeric($exception->getMessage()) ? $exception->getMessage() : 0;
            }
            return ErrorCode::getMessage($code);
        } else {
            throw $exception;
        }
    }
}
