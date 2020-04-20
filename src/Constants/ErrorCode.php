<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Spool\Pedis\Constants;

/**
 * Description of ApiErrorCode
 *
 * @author 陈浩波
 */
class ErrorCode extends AbstractConstants
{
    /**
     * @Message('成功!')
     */
    const SUCCESS = 200;
    /**
     * @Message('$config not is string or array!')
     */
    const CONFIG_NOT_STRING_OR_ARRAY = 1401;
}
