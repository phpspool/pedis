<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Exceptions;

use Exception;
use Spool\Pedis\Constants\ErrorCode;
use Spool\Pedis\Lib\Log;
/**
 * Description of PedisException
 *
 * @author 陈浩波
 */
class PedisException extends Exception
{
    public static function render(\Exception $exception)
    {
        if ($exception instanceof PedisException) {
            $code = $exception->getCode();
            if (!$code || $code < 0) {
                $code = is_numeric($exception->getMessage()) ? $exception->getMessage() : 0;
            }
	    Log::error(ErrorCode::getMessage($code));
            return ['code' => $code, 'msg' => ErrorCode::getMessage($code)];
        } else {
//            throw $exception;
	    Log::info($exception->getMessage());
	    return ['code' => $exception->getCode(), 'msg' => $exception->getMessage()];
        }
    }
    public function test(\Exception $exc)
    {
	Log::error($exc->getMessage());
    }
}
